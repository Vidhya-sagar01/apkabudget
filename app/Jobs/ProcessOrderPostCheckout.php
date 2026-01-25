<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\ZoneProvider;
use App\Models\User;
use App\Models\Notification;
use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessOrderPostCheckout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $order;
    protected $user_id;
    protected $subcategory_id;
    public function __construct(Order $order, $user_id, $subcategory_id)
    {
        $this->order = $order;
        $this->user_id = $user_id;
        $this->subcategory_id = $subcategory_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;
        $bookingId = $order->booking_id;
        $total_price = $order->total_price;
        $zone_id = $order->zone_id;

        $zoneProviderIds = ZoneProvider::where('zone_id', $zone_id)->pluck('user_id');
        if ($zoneProviderIds->isNotEmpty()) {
            $serviceProviders = User::whereIn('id', $zoneProviderIds)
                ->where('role', 2)
                ->whereNotNull('device_token')
                ->pluck('device_token')
                ->toArray();

            $title = 'New Booking Received!';
            $message = "You have received a new booking (ID: {$bookingId}). Total Amount: ₹{$total_price}.";

            foreach ($zoneProviderIds as $providerId) {
                Notification::create([
                    'user_id' => $providerId,
                    'title'   => $title,
                    'message' => $message
                ]);
            }

            if (!empty($serviceProviders)) {
                app('App\Services\NotificationService')->sendPushNotification($serviceProviders, $title, $message);
            }
        }
        $user = User::find($this->user_id);
        if (!empty($user->device_token)) {
            $userTitle = 'Booking Placed Successfully!';
            $userMessage = "Your booking (ID: {$bookingId}) has been placed successfully. Total Amount: ₹{$total_price}. Our providers will reach out soon.";

            Notification::create([
                'user_id' => $user->id,
                'title'   => $userTitle,
                'message' => $userMessage
            ]);

            app('App\Services\NotificationService')->sendPushNotification([$user->device_token], $userTitle, $userMessage);
        }

        Cart::where('user_id', $this->user_id)->where('subcategory_id', $this->subcategory_id)->forceDelete();
    }
}
