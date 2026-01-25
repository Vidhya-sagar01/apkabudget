<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ZoneRoundRobinTracker;
use App\Models\Order;
use App\Models\Zone;
use App\Models\Subscription;
use App\Models\SubCategory;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\OrderAssignmentAttempt;

class AutoAssignPendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-assign-pending-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically assigns booking to next eligible provider if previous did not accept in 1 min';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffTime = now()->subMinutes(2);

        $orders = Order::where('status', 'placed')
            // ->whereNotNull('assigned_at')
            // ->whereNotNull('provider_id')
            ->where('assigned_at', '<=', $cutoffTime)
            ->orderBy('assigned_at')
            ->get()
            ->groupBy('zone_id');

        foreach ($orders as $zoneId => $zoneOrders) {
            DB::beginTransaction();
            \Log::info("Processing zone: $zoneId");
            try {
                $zone = Zone::find($zoneId);
                if (!$zone) {
                    \Log::warning("Zone not found: $zoneId");
                    DB::rollBack();
                    continue;
                }

                // $providers = $zone->providers()->orderBy('created_at')->get();
                // $eligibleProviders = $providers->filter(function ($provider) {
                //     return Subscription::getActiveSubscription($provider->id, 1) &&
                //         Subscription::getActiveSubscription($provider->id, 2);
                // });

                // $providerIds = $eligibleProviders->pluck('id')->toArray();

                // if (empty($providerIds)) {
                //     DB::rollBack();
                //     continue;
                // }

                $tracker = ZoneRoundRobinTracker::firstOrCreate(
                    ['zone_id' => $zone->id],
                    ['last_assigned_user_id' => null]
                );

                $currentLastAssignedUserId = $tracker->last_assigned_user_id;
                // $lastIndex = $currentLastAssignedUserId
                //     ? array_search($currentLastAssignedUserId, $providerIds)
                //     : -1;

                // $nextIndex = ($lastIndex + 1) % count($providerIds);
                // $nextProviderId = $providerIds[$nextIndex];

                $staleOrders = $zoneOrders->filter(function ($order) use ($cutoffTime) {
                    return $order->assigned_at <= $cutoffTime && $order->status === 'placed';
                });

                if ($staleOrders->isEmpty()) {
                    \Log::info("No stale orders found in zone: $zoneId");
                    DB::rollBack();
                    continue;
                }

                foreach ($staleOrders as $order) {
                    \Log::info("Processing Order ID: {$order->id}");
                    $subcategory = SubCategory::find($order->subcategory_id);
                    if (!$subcategory) {
                        \Log::warning("SubCategory not found for Order ID: {$order->id}");
                        continue;
                    }
                    $categoryId = $subcategory->category_id;

                    $providers = $zone->providers()
                        ->where('category_id', $categoryId)
                        ->where('is_blocked', 0)
                        ->orderBy('created_at')
                        ->get();

                    $eligibleProviders = $providers->filter(function ($provider) {
                        return Subscription::getActiveSubscription($provider->id, 1)
                            // &&
                            // Subscription::getActiveSubscription($provider->id, 2)
                        ;
                    });

                    $providerIds = $eligibleProviders->pluck('id')->toArray();
                    \Log::info("Eligible Providers: ", $providerIds);

                    if (empty($providerIds)) {
                        \Log::info("No eligible providers for Order ID: {$order->id}");
                        continue;
                    }

                    // ⛔ Get already skipped providers
                    $alreadySkippedIds = OrderAssignmentAttempt::where('order_id', $order->id)
                        ->where('status', 'skipped')
                        ->pluck('provider_id')
                        ->toArray();

                    // ✅ Exclude skipped providers
                    $availableProviderIds = array_values(array_diff($providerIds, $alreadySkippedIds));
                    \Log::info("🧾 Available Providers (skip ke baad): ", $availableProviderIds);

                    if (empty($availableProviderIds)) {
                        \Log::warning("🔁 Sab providers skip ho gaye Order ID: {$order->id}, isliye null set kar rahe.");
                        $order->update([
                            'provider_id' => null,
                            'assigned_at' => null,
                        ]);
                        continue;
                    }

                    // $lastIndex = $currentLastAssignedUserId
                    //     ? array_search($currentLastAssignedUserId, $availableProviderIds)
                    //     : -1;

                    // $nextIndex = ($lastIndex + 1) % count($availableProviderIds);
                    // $nextProviderId = $availableProviderIds[$nextIndex];

                    if ($order->provider_id) {
                        
                         $skippedProviderId = $order->provider_id;

    // 👇 Skip insert if already skipped
    $alreadySkipped = OrderAssignmentAttempt::where([
        'order_id' => $order->id,
        'provider_id' => $skippedProviderId,
        'status' => 'skipped'
    ])->exists();
    if (!$alreadySkipped) {
        $skippedSubscription = Subscription::getActiveSubscription($skippedProviderId, 1);
        $skippedPlanId = $skippedSubscription ? $skippedSubscription->id : null;

        OrderAssignmentAttempt::create([
            'order_id'    => $order->id,
            'zone_id'     => $zone->id,
            'provider_id' => $skippedProviderId,
            'status'      => 'skipped',
            'plan_id'     => $skippedPlanId,
        ]);

        \Log::info("🛑 Provider skip kiya gaya: $skippedProviderId for Order ID: {$order->id}");
    } else {
        \Log::info("⚠️ Already skipped before: $skippedProviderId for Order ID: {$order->id}");
    }
                        // $skippedProviderId = $order->provider_id;
                        // $skippedSubscription = Subscription::getActiveSubscription($skippedProviderId, 1);
                        // $skippedPlanId = $skippedSubscription ? $skippedSubscription->id : null;

                        // OrderAssignmentAttempt::create([
                        //     'order_id'   => $order->id,
                        //     'zone_id'    => $zone->id,
                        //     'provider_id' => $skippedProviderId,
                        //     'status'     => 'skipped',
                        //     'plan_id'    => $skippedPlanId,
                        // ]);
                        // \Log::info("🛑 Provider skip kiya gaya: $skippedProviderId for Order ID: {$order->id}");
                    }

                    $lastIndex = $currentLastAssignedUserId
                        ? array_search($currentLastAssignedUserId, $availableProviderIds)
                        : -1;

                    $nextIndex = ($lastIndex + 1) % count($availableProviderIds);
                    $nextProviderId = $availableProviderIds[$nextIndex];

                    \Log::info("➡️ Assigning next provider ID: $nextProviderId for Order ID: {$order->id}");

                    $order->update([
                        'provider_id'  => $nextProviderId,
                        'assigned_at' => now(),
                    ]);

                    $provider = $eligibleProviders->where('id', $nextProviderId)->first();
                    if ($provider && $provider->device_token) {
                        $title = 'New Booking Assigned to You!';
                        $message = "Booking (ID: {$order->booking_id}) has been assigned to you.";

                        Notification::create([
                            'user_id' => $provider->id,
                            'title'   => $title,
                            'message' => $message
                        ]);

                        app('App\Services\NotificationService')->sendPushNotification([$provider->device_token], $title, $message);
                    }

                    $currentLastAssignedUserId = $nextProviderId;
                }

                $tracker->update(['last_assigned_user_id' => $currentLastAssignedUserId]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Cron Order Assignment Error: ' . $e->getMessage());
            }
        }
        return 0;
    }
}
