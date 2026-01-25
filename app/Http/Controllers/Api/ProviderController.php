<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Category;
use App\Models\IdentityType;
use App\Models\Transaction;
use App\Models\Review;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Plan;
use App\Models\OrderItem;
use App\Models\PriceList;
use App\Models\Subscription;
use Twilio\Rest\Client;
use App\Models\User;
use App\Models\Zone;
use App\Models\ZoneProvider;
use App\Models\CancelledOrder;
use App\Models\TermCondition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrderAssignmentAttempt;
use App\Models\Contact;
use App\Models\Part;

class ProviderController extends Controller
{

    // protected function dashboard()
    // {
    //     try {
    //         if (!$this->user) {
    //             return $this->errorResponse('User not found', 404);
    //         }
    //         $providerId = $this->user->id;

    //         $totalEarning = Order::where('provider_id', $providerId)
    //             ->where('status', 'completed')
    //             ->sum('total_price');

    //         $totalLeads = 0;
    //         $completedLeads = 0;
    //         $remainingLeads = 0;
    //         $skippedLeads = 0;

    //         $activeSubscription = Subscription::getActiveSubscription($providerId, 1);
    //         if ($activeSubscription) {
    //             $plan = Plan::find($activeSubscription->plan_id);
    //             $totalLeads  = $plan->leads ?? 0;

    //             $completedLeads  = Order::where('subscription_id', $activeSubscription->id)->where('provider_id', $providerId)->whereIn('status', ['completed','accepted'])->count();
    //             $cancelledLeads = CancelledOrder::where('provider_id', $providerId)->where('subscription_id', $activeSubscription->id)->count();
    //             $orderIds = Order::where('subscription_id', $activeSubscription->id)->pluck('id');
    //             $skippedLeads = OrderAssignmentAttempt::
    //             where('provider_id', $providerId)
    //             ->where('status', 'skipped')
    //             ->count();
    //             // $zoneorder = 0;
    //             // $assignedZone = DB::table('zone_provider')
    //             //     ->where('user_id', $providerId)
    //             //     ->value('zone_id');

    //             // if ($assignedZone) {
    //                 // $zoneorder = Order::where('zone_id', $assignedZone)->take(20)->get();
    //                 // $orderCount = $zoneorder->count();
    //             // }
    //             $remainingLeads = max($totalLeads - ($completedLeads + $cancelledLeads + $skippedLeads), 0);
    //         }
    //         // if($this->user->id == 1759){
    //         $overviews = [
    //             'total_earning' => $totalEarning ?? 0,
    //             'total_leads' => $totalLeads,
    //             'complete_leads' => $completedLeads + $skippedLeads,
    //             'pending_leads' => $remainingLeads,
    //         ];
    //         // }else{
    //                     // $overviews = [
    //                     //     'total_earning' => $totalEarning ?? 0,
    //                     //     'total_leads' => $totalLeads,
    //                     //     'complete_leads' => $completedLeads + $skippedLeads + $orderCount,
    //                     //     'pending_leads' => $remainingLeads - $orderCount,
    //                     // ];
    //         // }

    //         $reviews = Review::where('reviewee_id', $this->user->id)->with(['reviewer:id,name,profile'])->take(6)->select('id', 'reviewer_id', 'rating', 'review', 'created_at')->get();

    //         return $this->successResponse(['overviews' => $overviews, 'reviews' => $reviews], 'Dashboard data retrieved successfully');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    
    
    protected function dashboard()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }
            $providerId = $this->user->id;

            $totalEarning = Order::where('provider_id', $providerId)
                ->where('status', 'completed')
                ->sum('total_price');

            $totalLeads = 0;
            $completedLeads = 0;
            $remainingLeads = 0;
            $skippedLeads = 0;

            $activeSubscription = Subscription::getActiveSubscription($providerId, 1);
            if ($activeSubscription) {
                $plan = Plan::find($activeSubscription->plan_id);
                $totalLeads  = $plan->leads ?? 0;

                $completedLeads  = Order::where('subscription_id', $activeSubscription->id)->where('provider_id', $providerId)->whereIn('status', ['completed','accepted'])->count();
                
                // dd($completedLeads);
                $cancelledLeads = CancelledOrder::where('provider_id', $providerId)->where('subscription_id', $activeSubscription->id)->count();
                $orderIds = Order::where('subscription_id', $activeSubscription->id)->pluck('id');
                $skippedLeads = OrderAssignmentAttempt::
                where('provider_id', $providerId)
                ->where('status', 'skipped')
                ->count();
                // $zoneorder = 0;
                // $assignedZone = DB::table('zone_provider')
                //     ->where('user_id', $providerId)
                //     ->value('zone_id');

                // if ($assignedZone) {
                    // $zoneorder = Order::where('zone_id', $assignedZone)->take(20)->get();
                    // $orderCount = $zoneorder->count();
                // }
                $remainingLeads = max($totalLeads - ($completedLeads + $cancelledLeads + $skippedLeads), 0);
            }
            // if($this->user->id == 1759){
            $overviews = [
                'total_earning' => $totalEarning ?? 0,
                'total_leads' => $totalLeads,
                'complete_leads' => $completedLeads + $skippedLeads,
                'pending_leads' => $remainingLeads,
            ];
            // }else{
                        // $overviews = [
                        //     'total_earning' => $totalEarning ?? 0,
                        //     'total_leads' => $totalLeads,
                        //     'complete_leads' => $completedLeads + $skippedLeads + $orderCount,
                        //     'pending_leads' => $remainingLeads - $orderCount,
                        // ];
            // }

            $reviews = Review::where('reviewee_id', $this->user->id)->with(['reviewer:id,name,profile'])->take(6)->select('id', 'reviewer_id', 'rating', 'review', 'created_at')->get();

            return $this->successResponse(['overviews' => $overviews, 'reviews' => $reviews], 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    

    protected function viewProviderDashboard()
    {
       return view('Website.privacy_policy');  
    }

    protected function viewUserDashboard()
    {
       return view('Website.privacy_policy');  
    }

    
    protected function countries()
    {
        try {
            $countries = Country::where('status', 1)->get();

            if ($countries->isEmpty()) {
                return $this->errorResponse('No countries found', 404);
            }

            return $this->successResponse($countries, 'Countries retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function states(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'country_id' => 'required'
        ]);

        if ($validation) return $validation;

        try {
            $country_id = $request->country_id;
            $states = State::where('country_id', $country_id)->select('id', 'name')->get();

            if ($states->isEmpty()) {
                return $this->errorResponse('No state found', 404);
            }

            return $this->successResponse($states, 'States retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function cities(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'state_id' => 'required'
        ]);

        if ($validation) return $validation;

        try {
            $state_id = $request->state_id;
            $cities = City::where('state_id', $state_id)->select('id', 'name')->get();

            if ($cities->isEmpty()) {
                return $this->errorResponse('No state found', 404);
            }

            return $this->successResponse($cities, 'States retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function identity_types()
    {
        try {
            $identity_types = IdentityType::where('status', 1)->select('id', 'identity')->get();

            if ($identity_types->isEmpty()) {
                return $this->errorResponse('No countries found', 404);
            }

            return $this->successResponse($identity_types, 'Identity Types retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function plans(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'type' => 'required|in:1,2' //1-subscriptions plan,2-security plan
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $plan = Plan::where('category_id', $this->user->category_id)->where('type', $request->type)->get();

            if (!$plan) {
                return $this->errorResponse('Invalid plan selected', 400);
            }

            return $this->successResponse($plan, 'Plan retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    //payment time hit api
    protected function payment_status(Request $request)
    {

        $validation = $this->validateRequest($request, [
            // 'plan_id' => 'required|exists:plans,id',
            // 'transaction_id' => 'required',
            'status' => 'required|in:success,failed,pending',
            // 'amount' => 'required'
        ]);

        // if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            // $plan = Plan::find($request->plan_id);

            // if (!$plan) {
            //     return $this->errorResponse('Invalid plan selected', 400);
            // }
            // Check Existing Subscription of Same Type
            // $existingSubscription = Subscription::where([
            //     ['user_id', $this->user->id],
            //     ['type', $plan->type]
            // ])->first();

            // $subscriptionData = [
            //     'type' => $plan->type,
            //     'user_id' => $this->user->id,
            //     'plan_id' => $request->plan_id,
            //     'status' => ($request->status == 'success') ? 'active' : 'pending',
            //     'start_date' => ($request->status == 'success') ? now() : null,
            //     'end_date' => ($request->status == 'success') ? now()->addDays($plan->duration) : null
            // ];
            // Update or Create Subscription
            // if ($existingSubscription) {
            //     $existingSubscription->update($subscriptionData);
            // } else {
            //     $existingSubscription = Subscription::create($subscriptionData);
            // }


            // $transactionData = [
            //     'type' => $plan->type,
            //     'user_id' => $this->user->id,
            //     'transaction' => 2, // 2 = debit
            //     'amount' => $request->amount,
            //     'transaction_id' => $request->transaction_id,
            //     'subscription_id' => $request->plan_id,
            //     'status' => $request->status
            // ];

            // Transaction::create($transactionData);

            return $this->successResponse('Transaction added successfully', $request->status == 'success' ? 'Success' : 'Payment failed, please try again');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    //     protected function bookings(Request $request)
    //     {
    //         try {
    //             if (!$this->user) {
    //                 return $this->errorResponse('User not found', 404);
    //             }
    //             $status = $request->input('status', 'placed');
    //             $validStatuses = ['placed', 'accepted', 'completed', 'cancelled'];
    //         if (!in_array($status, $validStatuses)) {
    //             return $this->errorResponse('Invalid status provided', 404);
    //         }
    //              $assignedZoneIds = ZoneProvider::where('user_id', $this->user->id)->pluck('zone_id');
    //             if ($assignedZoneIds->isEmpty()) {
    //                 return $this->errorResponse('No zones assigned to this provider', 404);
    //             }
    //             // $zones = Zone::whereIn('id', $assignedZoneIds)->get(['id', 'name', 'boundary']);
    //             // if ($zones->isEmpty()) {
    //             //     return $this->errorResponse('No zones found', 404);
    //             // }
    //              $bookingsQuery = Order::with([
    //             'user:id,name,mobile_no',
    //             'subCategory:id,name,image',
    //             'address:id,address,latitude,longitude,flat_no,landmark',
    //         ])
    //             ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'address_id', 'total_price', 'status', 'provider_id', 'created_at','slot_date','slot_start_time','slot_end_time')
    //             // ->whereIn('zone_id', $assignedZoneIds)
    //                 ->where('status', $status)
    //             // ->where('provider_id', $this->user->id)
    //             ->orderBy('id', 'DESC');
    // // dd($bookingsQuery->get());
    //         // Condition for accepted and completed bookings only visible to assigned provider
    //         // if (in_array($status, ['accepted', 'completed'])) {
    //         //     $bookings = $bookings->where('provider_id', $this->user->id);
    //         // }

    //  if (in_array($status, ['accepted', 'completed'])) {
    //                 $bookingsQuery->where('provider_id', $this->user->id);
    //             } elseif ($status === 'placed') {
    //                 $bookingsQuery->where(function ($query) {
    //                     $query->whereNull('provider_id')
    //                         ->orWhere('provider_id', $this->user->id);
    //                 })->whereIn('zone_id', $assignedZoneIds);
    //             }
    //         $bookings = $bookingsQuery->get();

    //         // Filter bookings based on zone boundaries
    //         // $filteredBookings = $bookings->filter(function ($booking) use ($zones) {
    //         //     if (!$booking->address) {
    //         //         return false;
    //         //     }
    //         //     $lat = (float) $booking->address->latitude;
    //         //     $lng = (float) $booking->address->longitude;

    //         //     foreach ($zones as $zone) {
    //         //         $boundaries = json_decode($zone->boundary, true);

    //         //         if (!is_array($boundaries)) {
    //         //             throw new \Exception("Invalid boundary data for zone: " . $zone->id);
    //         //         }

    //         //         if ($this->isPointInPolygon($lat, $lng, $boundaries)) {
    //         //             return true;
    //         //         }
    //         //     }
    //         //     return false;
    //         // })->values();

    //         // Image URL transformation
    //         $bookings->transform(function ($booking) {
    //             if ($booking->subCategory && $booking->subCategory->image) {
    //                 $booking->subCategory->image = url($booking->subCategory->image);
    //             }
    //             return $booking;
    //         });

    //         if ($bookings->isEmpty()) {
    //             return $this->errorResponse('No Bookings found', 404);
    //         }

    //         return $this->successResponse($bookings, 'Bookings retrieved successfully');
    //         } catch (\Exception $e) {
    //             return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //         }
    //     }
    //     protected function bookings(Request $request)
    // {
    //     try {
    //         if (!$this->user) {
    //             return $this->errorResponse('User not found', 404);
    //         }

    //         $status = $request->input('status', 'placed');
    //         $validStatuses = ['placed', 'accepted', 'completed', 'cancelled'];

    //         if (!in_array($status, $validStatuses)) {
    //             return $this->errorResponse('Invalid status provided', 404);
    //         }

    //         $assignedZoneIds = ZoneProvider::where('user_id', $this->user->id)->pluck('zone_id');
    //         if ($assignedZoneIds->isEmpty()) {
    //             return $this->errorResponse('No zones assigned to this provider', 404);
    //         }

    //         $zones = Zone::whereIn('id', $assignedZoneIds)->get(['id', 'name', 'boundary']);
    //         if ($zones->isEmpty()) {
    //             return $this->errorResponse('No zones found', 404);
    //         }

    //         $bookingsQuery = Order::with([
    //             'user:id,name,mobile_no',
    //             'subCategory:id,name,image',
    //             'address:id,address,latitude,longitude,flat_no,landmark',
    //         ])
    //             ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'address_id', 'total_price', 'status', 'provider_id', 'created_at')
    //             ->where('status', $status)
    //             ->orderBy('id', 'DESC');

    //         if (in_array($status, ['accepted', 'completed'])) {
    //             $bookingsQuery->where('provider_id', $this->user->id);
    //         } elseif ($status === 'placed') {
    //             $bookingsQuery->where(function ($query) {
    //                 $query->whereNull('provider_id')
    //                     ->orWhere('provider_id', $this->user->id);
    //             });
    //         }

    //         $bookings = $bookingsQuery->get();

    //         if ($status === 'placed') {
    //             $bookings = $bookings->filter(function ($booking) use ($zones) {
    //                 if (!$booking->address || $booking->provider_id !== null) {
    //                     return true;
    //                 }

    //                 $lat = (float) $booking->address->latitude;
    //                 $lng = (float) $booking->address->longitude;

    //                 foreach ($zones as $zone) {
    //                     $boundaries = json_decode($zone->boundary, true);
    //                     if (!is_array($boundaries)) {
    //                         throw new \Exception("Invalid boundary data for zone: " . $zone->id);
    //                     }
    //                     if ($this->isPointInPolygon($lat, $lng, $boundaries)) {
    //                         return true;
    //                     }
    //                 }
    //                 return false;
    //             });
    //         }

    //         $bookings->transform(function ($booking) {
    //             if ($booking->subCategory && $booking->subCategory->image) {
    //                 $booking->subCategory->image = url($booking->subCategory->image);
    //             }
    //             return $booking;
    //         });

    //         if ($bookings->isEmpty()) {
    //             return $this->errorResponse('No Bookings found', 404);
    //         }

    //         return $this->successResponse($bookings, 'Bookings retrieved successfully');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    protected function isPointInPolygon($latitude, $longitude, $polygon)
    {
        if (!is_array($polygon) || count($polygon) < 3) {
            throw new \Exception("Invalid polygon data: Polygon must have at least 3 points.");
        }

        foreach ($polygon as $index => $point) {
            if (!isset($point['lng']) || !isset($point['lat'])) {
                throw new \Exception("Invalid polygon data at index $index: Missing lat or lng.");
            }
        }

        $inside = false;
        $x = (float)$longitude;
        $y = (float)$latitude;
        $numPoints = count($polygon);
        $j = $numPoints - 1;

        for ($i = 0; $i < $numPoints; $j = $i++) {
            $xi = (float)$polygon[$i]['lng'];
            $yi = (float)$polygon[$i]['lat'];
            $xj = (float)$polygon[$j]['lng'];
            $yj = (float)$polygon[$j]['lat'];

            $intersect = (($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

     protected function bookings(Request $request)
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $status = $request->input('status', 'placed');
            $validStatuses = ['placed', 'accepted', 'completed', 'cancelled','pending','complaint'];

            if (!in_array($status, $validStatuses)) {
                return $this->errorResponse('Invalid status provided', 404);
            }

            $assignedZoneIds = ZoneProvider::where('user_id', $this->user->id)->pluck('zone_id');
            if ($assignedZoneIds->isEmpty()) {
                return $this->errorResponse('No zones assigned to this provider', 404);
            }

            $providerCategoryId = $this->user->category_id;
            $providerLat = $this->user->latitude;
            $providerLng = $this->user->longitude;

            $query = Order::with([
                'user:id,name,mobile_no',
                'subCategory:id,name,image,category_id',
                'address:id,address,latitude,longitude,flat_no,landmark',
                'complaints:id,order_id,message,status,created_at'
            ])
            ->whereHas('subCategory', fn($q) => $q->where('category_id', $providerCategoryId))
            ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'address_id', 'total_price', 'status', 'provider_id', 'created_at', 'slot_date', 'slot_start_time', 'slot_end_time')
            ->orderByDesc('id');

            switch ($status) {
                case 'cancelled':
                    $cancelledIds = CancelledOrder::where('provider_id', $this->user->id)->pluck('order_id');
                    $query->whereIn('id', $cancelledIds);
                    break;

                case 'accepted':
                case 'completed':
                    $query->where('status', $status)->where('provider_id', $this->user->id);
                    break;

                case 'placed':
                    $cancelledIds = CancelledOrder::where('provider_id', $this->user->id)->pluck('order_id');
                    $query->where('status', 'placed')
                        ->whereNotIn('id', $cancelledIds)
                        ->whereIn('zone_id', $assignedZoneIds);
                    break;

                case 'pending':
                    $query->whereIn('zone_id', $assignedZoneIds);
                    break;

                case 'complaint':
                    $query->whereHas('complaints', function ($q) {
                        $q->where('provider_id', $this->user->id);
                    });
                    break;
            }

            // 👇 Dynamic limit from params, default 50
            $limit = $request->input('limit', 50);

            $bookings = $query->take($limit)->get()->map(function ($booking, $index) use ($providerLat, $providerLng,$status) {
                if (!empty($booking->subCategory->image)) {
                    $booking->subCategory->image = url($booking->subCategory->image);
                }
                if ($booking->address && $booking->address->latitude && $booking->address->longitude) {
                    if ($index < 10) { 
                        $origin = "{$providerLat},{$providerLng}";
                        $destination = "{$booking->address->latitude},{$booking->address->longitude}";
                        $distanceData = $this->getDistanceAndDuration($origin, $destination);

                        $booking->distance = $distanceData['distance'];
                        $booking->duration = $distanceData['duration'];
                    } else {
                        $booking->distance = null;
                        $booking->duration = null;
                    }
                }
                if ($status === 'complaint' && $booking->complaints->isNotEmpty()) {
                    $latestComplaint = $booking->complaints->last();
                    $booking->complaint_message = $latestComplaint->message;
                    $booking->complaint_date = $latestComplaint->created_at->toDateTimeString();
                } else {
                    $booking->complaint_message = null;
                    $booking->complaint_date = null;
                }
                return $booking;
            });

            if ($status === 'complaint') {
                $bookings = $bookings->sortByDesc(function ($booking) {
                    return optional($booking->complaints->last())->created_at;
                })->values();
            }

            if ($bookings->isEmpty()) {
                return $this->errorResponse('No Bookings found', 404);
            }

            return $this->successResponse($bookings, 'Bookings retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function getDistanceAndDuration($origin, $destination)
    {
        $apiKey = config('services.google_maps.key'); // move to config/services.php
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . urlencode($origin) . "&destinations=" . urlencode($destination) . "&mode=driving&language=en-EN&key=" . $apiKey;

        try {
            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if ($data['status'] === 'OK') {
                $element = $data['rows'][0]['elements'][0];
                if ($element['status'] === 'OK') {
                    return [
                        'distance' => $element['distance']['text'],
                        'duration' => $element['duration']['text']
                    ];
                }
            }
        } catch (\Exception $e) {
            // Log or ignore
        }

        return ['distance' => null, 'duration' => null];
    }
    protected function booking_detail(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'id' => 'required|exists:orders,id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $id = $request->id;

            $booking = Order::with([
                'user:id,name,mobile_no',
                'subCategory:category_id,id,name',
                'address:id,address,latitude,longitude,flat_no,landmark',
                'complaints:id,order_id,provider_id,message,status,created_at',
                // Fetching regular services (extra_service == 0)
                'orderItems' => function ($query) {
                    $query->where('extra_service', 0)
                        ->select('id', 'order_id', 'service_id', 'quantity', 'unit_price', 'total_price', 'extra_service');
                },
                'orderItems.service:id,service_name,image',
                // Fetching added services (extra_service == 1)
                'addedServices' => function ($query) {
                    $query->where('extra_service', 1)
                        ->with(['priceList:id,detail'])
                        ->select('id', 'order_id', 'service_id', 'quantity', 'total_price', 'extra_service');
                }
            ])
                ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'slot_date', 'slot_start_time', 'slot_end_time', 'address_id', 'total_price', 'payment_method', 'status', 'created_at')
                ->where('id', $id)
                ->first();

            if (!$booking) {
                return $this->errorResponse('Booking not found', 404);
            }

            // Check if orderItems exist before transforming
            if ($booking->orderItems->isNotEmpty()) {
                $booking->orderItems->map(function ($item) {
                    if ($item->service && $item->service->image) {
                        $item->service->image = url($item->service->image);
                    }
                    return $item;
                });
            }

            // Merge the added services into the main booking response
            $booking->added_services = $booking->addedServices;

            // Calculate final total_price dynamically
            $orderItemsTotal = $booking->orderItems->sum('total_price');
            $addedServicesTotal = $booking->addedServices->sum('total_price');

            $booking->total_price = $orderItemsTotal + $addedServicesTotal;
            $complaint = $booking->complaints->where('provider_id', $this->user->id)->last();

            if ($complaint) {
                $booking->complaint_message = $complaint->message;
                $booking->complaint_date = $complaint->created_at->toDateTimeString();
            } else {
                $booking->complaint_message = null;
                $booking->complaint_date = null;
            }

            return $this->successResponse($booking, 'Booking details retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    // protected function booking_action(Request $request)
    // {
    //     $validation = $this->validateRequest($request, [
    //         'id' => 'required|exists:orders,id',
    //         'status' => 'required|in:accepted,completed',
    //     ]);

    //     if ($validation) return $validation;

    //     try {
    //         if (!$this->user) {
    //             return $this->errorResponse('User not found', 404);
    //         }

    //         DB::beginTransaction();

    //         $order = Order::where('id', $request->id)
    //             ->lockForUpdate()
    //             ->first();

    //         if (!$order) {
    //             DB::rollBack();
    //             return $this->errorResponse('Order not found', 404);
    //         }
    //     if ($request->status === 'completed') {
    //             if ($order->status !== 'accepted') {
    //                 DB::rollBack();
    //                 return $this->errorResponse('Only accepted orders can be marked as completed.', 400);
    //             }

    //             if ($order->provider_id !== $this->user->id) {
    //                 DB::rollBack();
    //                 return $this->errorResponse('You are not authorized to complete this order.', 403);
    //             }

    //             $order->update([
    //                 'status' => 'completed',
    //                 'completed_date' => now()
    //             ]);

    //             DB::commit();
    //             return $this->successResponse('Booking completed successfully');
    //         }

    //         if ($request->status === 'accepted') {
    //             if ($order->status !== 'placed') {
    //                 DB::rollBack();
    //                 return $this->errorResponse('Order cannot be accepted. It is already in process or completed.', 400);
    //             }

    //             if ($order->provider_id && $order->provider_id != $this->user->id) {
    //                 DB::rollBack();
    //                 return $this->errorResponse('This order is assigned to another provider.', 403);
    //             }

    //             $activeSubscription = Subscription::getActiveSubscription($this->user->id, 1);
    //             $activeSecurity = Subscription::getActiveSubscription($this->user->id, 2);

    //             if (!$activeSubscription || !$activeSecurity) {
    //                 DB::rollBack();
    //                 return $this->errorResponse('Active subscription and security plan required.', 403);
    //             }
                
    //             $order->update([
    //                 'provider_id' => $this->user->id,
    //                 'subscription_id' => $activeSubscription->id,
    //                 'status' => 'accepted',
    //                 'accepted_date' => now()
    //             ]);
    //             OrderAssignmentAttempt::create([
    //         'order_id' => $order->id,
    //         'zone_id' => $order->zone_id,
    //         'provider_id' => $this->user->id,
    //         'status' => 'accepted',
    //     ]);
    //     DB::commit();
    //             return $this->successResponse('Booking accepted successfully');
    //         }

    //         // if ($request->status == 'accepted') {
    //         //     $activeSubscription = Subscription::getActiveSubscription($this->user->id, 1); // Type 1 = Subscription
    //         //     $activeSecurity = Subscription::getActiveSubscription($this->user->id, 2); // Type 2 = Security

    //         //     if (!$activeSubscription || !$activeSecurity) {
    //         //         DB::rollBack();
    //         //         return $this->errorResponse('You need an active subscription and security plan to accept this booking.', 403);
    //         //     }
    //         //     if ($order->status !== 'placed') {
    //         //         DB::rollBack();
    //         //         return $this->errorResponse('Order cannot be accepted. It is already in process or completed.', 400);
    //         //     }
    //         //     $hasActiveBooking = Order::where('provider_id', $this->user->id)
    //         //         ->where('status', 'accepted')
    //         //         ->where('id', '!=', $order->id) // avoid checking the same order
    //         //         ->exists();
    //         //     if ($hasActiveBooking) {
    //         //         DB::rollBack();
    //         //         return $this->errorResponse('You already have an active booking. Please complete it before accepting a new one.', 403);
    //         //     }
    //         //     $order->update([
    //         //         'provider_id' => $this->user->id,
    //         //         'status' => 'accepted',
    //         //         'subscription_id' => $activeSubscription->id,
    //         //     ]);
    //         //     DB::commit();
    //         //     return $this->successResponse('Booking accepted successfully');
    //         // }
    //         // if ($request->status == 'completed') {
    //         //     if ($order->status !== 'accepted') {
    //         //         DB::rollBack();
    //         //         return $this->errorResponse('Only accepted orders can be marked as completed.', 400);
    //         //     }
    //         //     if ($order->provider_id !== $this->user->id) {
    //         //         DB::rollBack();
    //         //         return $this->errorResponse('You are not authorized to complete this order.', 403);
    //         //     }
    //         //     $order->update(['status' => 'completed']);

    //         //     DB::commit();
    //         //     return $this->successResponse('Booking completed successfully');
    //         // }
    //         DB::rollBack();
    //         return $this->errorResponse('Invalid status provided', 400);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    
    
     protected function booking_action(Request $request)
        {
            $validation = $this->validateRequest($request, [
                'id' => 'required|exists:orders,id',
                'status' => 'required|in:accepted,completed',
            ]);

            if ($validation) return $validation;

            try {
                if (!$this->user) {
                    return $this->errorResponse('User not found', 404);
                }

                DB::beginTransaction();

                $order = Order::where('id', $request->id)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    DB::rollBack();
                    return $this->errorResponse('Order not found', 404);
                }

                // ------------------------
                // COMPLETED BOOKING LOGIC
                // ------------------------
                if ($request->status === 'completed') {
                    if ($order->status !== 'accepted') {
                        DB::rollBack();
                        return $this->errorResponse('Only accepted orders can be marked as completed.', 400);
                    }

                    if ($order->provider_id !== $this->user->id) {
                        DB::rollBack();
                        return $this->errorResponse('You are not authorized to complete this order.', 403);
                    }

                    $order->update([
                        'status' => 'completed',
                        'completed_date' => now()
                    ]);

                    DB::commit();
                    return $this->successResponse('Booking completed successfully');
                }

                // ------------------------
                // ACCEPTED BOOKING LOGIC
                // ------------------------
                if ($request->status === 'accepted') {
                    // Rule 1: Only one active booking at a time
                    // $hasActiveBooking = Order::where('provider_id', $this->user->id)
                    //     ->where('status', 'accepted')
                    //     ->exists();

                    // if ($hasActiveBooking) {
                    //     DB::rollBack();
                    //     return $this->errorResponse('You already have an active booking. Please complete it before accepting a new one.', 403);
                    // }

                    //Rule 2: Wait 24 hours after last completed booking
                    // $lastCompletedOrder = Order::where('provider_id', $this->user->id)
                    //     ->where('status', 'completed')
                    //     ->orderBy('completed_date', 'desc')
                    //     ->first();

                    // if ($lastCompletedOrder) {
                    //     $hoursSinceCompletion = now()->diffInHours($lastCompletedOrder->completed_date);
                    //     if ($hoursSinceCompletion < 24) {
                    //         DB::rollBack();
                    //         return $this->errorResponse('You can only accept a new booking 24 hours after your last completed booking.', 403);
                    //     }
                    // }

                    // Original validations
                    if ($order->status !== 'placed') {
                        DB::rollBack();
                        return $this->errorResponse('Order cannot be accepted. It is already in process or completed.', 400);
                    }


                    if ($order->provider_id && $order->provider_id != $this->user->id) {
                        DB::rollBack();
                        return $this->errorResponse('This order is assigned to another provider.', 403);
                    }


                    $activeSubscription = Subscription::getActiveSubscription($this->user->id, 1);
                    $activeSecurity = Subscription::getActiveSubscription($this->user->id, 2);

                    if (!$activeSubscription || !$activeSecurity) {
                        DB::rollBack();
                        return $this->errorResponse('Active subscription and security plan required.', 403);
                    }

                    
                    // Accept the order
                    $order->update([
                        'provider_id' => $this->user->id,
                        'subscription_id' => $activeSubscription->id,
                        'status' => 'accepted',
                        'accepted_date' => now()
                    ]);

                    OrderAssignmentAttempt::create([
                        'order_id' => $order->id,
                        'zone_id' => $order->zone_id,
                        'provider_id' => $this->user->id,
                        'status' => 'accepted',
                    ]);

                    DB::commit();
                    return $this->successResponse('Booking accepted successfully');
                }

                DB::rollBack();
                return $this->errorResponse('Invalid status provided', 400);

            } catch (\Exception $e) {
                DB::rollBack();
                return $this->errorResponse('Something went wrong', 500, [
                    'error' => $e->getMessage()
                ]);
            }
        }

    
    
    // protected function booking_detail(Request $request)
    // {
    //     $validation = $this->validateRequest($request, [
    //         'id' => 'required|exists:orders,id'
    //     ]);

    //     if ($validation) return $validation;

    //     try {
    //         if (!$this->user) {
    //             return $this->errorResponse('User not found', 404);
    //         }

    //         $id = $request->id;

    //         $booking = Order::with([
    //             'user:id,name,mobile_no',
    //             'subCategory:category_id,id,name',
    //             'address:id,address,latitude,longitude,flat_no,landmark',
    //             'orderItems' => function ($query) {
    //                 $query->where('extra_service', 0) // filter extra_service == 0
    //                     ->select('id', 'order_id', 'service_id', 'quantity', 'unit_price', 'total_price', 'extra_service');
    //             },
    //             'orderItems.service:id,service_name,image'
    //         ])
    //             ->select('id', 'booking_id', 'user_id', 'subcategory_id', 'slot_date', 'slot_start_time', 'slot_end_time', 'address_id', 'total_price', 'payment_method', 'status', 'created_at')
    //             ->where('id', $id)
    //             ->first();

    //         if (!$booking) {
    //             return $this->errorResponse('Booking not found', 404);
    //         }

    //         $booking->orderItems->transform(function ($item) {
    //             if ($item->service && $item->service->image) {
    //                 $item->service->image = url($item->service->image);
    //             }
    //             return $item;
    //         });

    //         return $this->successResponse($booking, 'Booking details retrieved successfully');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    

    // private function getNextProvider($providers, $lastProvider)
    // {
    //     $providers = $providers->toArray();

    //     if (!$lastProvider || !in_array($lastProvider, $providers)) {
    //         return $providers[0];
    //     }

    //     $currentIndex = array_search($lastProvider, $providers);
    //     $nextIndex = ($currentIndex + 1) % count($providers);

    //     return $providers[$nextIndex];
    // }

    private function sendNotification($order, $title, $message)
    {
        Notification::create([
            'user_id' => $order->user_id,
            'title'   => $title,
            'message' => $message
        ]);

        $userDeviceToken = User::where('id', $order->user_id)->value('device_token');
        if ($userDeviceToken) {
            $this->notificationService->sendPushNotification([$userDeviceToken], $title, $message);
        }

        return $this->successResponse("Booking {$order->status} successfully");
    }


    public function initiatePayment(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $plan = Plan::findOrFail($request->plan_id);
        $amount = $plan->price * 100; // Convert to paisa

        $apiKey = env('RAZORPAY_KEY_ID');
        $apiSecret = env('RAZORPAY_KEY_SECRET');

        // Generate unique transaction ID
        $transactionId = 'TXN-' . strtoupper(Str::random(10));

        // Create Razorpay order
        $response = Http::withBasicAuth($apiKey, $apiSecret)->post('https://api.razorpay.com/v1/orders', [
            'amount' => $amount,
            'currency' => 'INR',
            'receipt' => $transactionId,
            'payment_capture' => 1 // Auto capture payment
        ]);

        $responseData = $response->json();

        if (!empty($responseData['id'])) {
            // Save transaction to database
            Transaction::create([
                'user_id' => $this->user->id,
                'type' => $plan->type,
                'transaction_id' => $transactionId,
                'amount' => $plan->price,
                'status' => 'pending',
                'subscription_id' => $plan->id
            ]);

            return response()->json([
                'order_id' => $responseData['id'],
                'amount' => $amount,
                'currency' => 'INR',
                'key' => $apiKey,
                'name' => 'Your Company Name',
                'description' => $plan->name,
                'prefill' => [
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'contact' => $this->user->mobile_no
                ],
                'theme' => ['color' => '#3399cc']
            ]);
        }

        return response()->json(['error' => 'Failed to create Razorpay order'], 500);
    }

    protected function makeCall(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'to' => 'required|numeric|digits:10', // Customer ka number
        ]);


        $toNumber = '+91' . $request->to; // E.164 format for India (+91)

        // Twilio API endpoint
        $url = "https://api.twilio.com/2010-04-01/Accounts/$accountSid/Calls.json";

        // Prepare data for the API request
        $postData = http_build_query([
            'From' => $fromNumber,
            'To' => $toNumber,
            'Url' => 'https://api.apkabudget.com/public/voice.xml', // TwiML instructions
        ]);

        // Initialize cURL session
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$accountSid:$authToken");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        // Handle Twilio's response
        if ($httpCode == 201) {
            return response()->json(['message' => 'Call initiated successfully!', 'response' => json_decode($response)], 200);
        } else {
            return response()->json(['message' => 'Failed to initiate call', 'error' => json_decode($response)], $httpCode);
        }
    }
    protected function zones()
    {
        try {

            $zones = Zone::select('id', 'name')->get();

            return $this->successResponse($zones, 'Zone retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function add_service(Request $request)
    {
        // Validation
        $validation = $this->validateRequest($request, [
            'order_id' => 'required|exists:orders,id',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:price_lists,id',
            'services.*.qty' => 'required|integer|min:0'
        ]);

        if ($validation) return $validation;
        try {
            // Fetch price lists and existing order items in one go
            $serviceIds = collect($request->services)->pluck('service_id');
            $priceLists = PriceList::whereIn('id', $serviceIds)->get()->keyBy('id');
            $existingItems = OrderItem::where('order_id', $request->order_id)
                ->whereIn('service_id', $serviceIds)
                ->where('extra_service', 1)
                ->get()
                ->keyBy('service_id');

            // Prepare data for batch upsert
            $data = [];

            foreach ($request->services as $service) {
                $serviceId = $service['service_id'];
                $qty = $service['qty'];

                if ($qty === 0) {
                    if (isset($existingItems[$serviceId])) {
                        $existingItems[$serviceId]->delete();
                    }
                    continue; // skip further processing for this
                }

                if (!isset($priceLists[$serviceId])) {
                    return $this->errorResponse('Service not found in price list', 400);
                }

                $price = $priceLists[$serviceId];
                $unitPrice = $price->charge + $price->labour_charge;
                $total = $unitPrice * $qty;

                if (isset($existingItems[$serviceId])) {

                    // If exists, update the existing record in the batch
                    $existingItems[$serviceId]->update([
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => $total,
                    ]);
                } else {
                    // If not exists, add new record to the batch data
                    $data[] = [
                        'order_id' => $request->order_id,
                        'service_id' => $serviceId,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total_price' => $total,
                        'extra_service' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }


            // Perform batch insert using upsert
            if (!empty($data)) {
                OrderItem::upsert($data, ['order_id', 'service_id', 'extra_service'], ['quantity', 'unit_price', 'total_price', 'updated_at']);
            }

            return $this->successResponse('Services added/updated successfully');
        } catch (\Exception $e) {
            // Proper exception handling
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function invoice(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'order_id' => 'required|exists:orders,id',
        ]);

        if ($validation) return $validation;
        try {

            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $order_id = $request->order_id;

            $booking = Order::with([
                'user:id,name,mobile_no',
                'subCategory:category_id,id,name',
                'address:id,address,latitude,longitude,flat_no,landmark',
                'orderItems' => function ($q) {
                    $q->select('id', 'order_id', 'service_id', 'quantity', 'unit_price', 'total_price', 'extra_service');
                },
                'orderItems.service:id,service_name,image',
                'orderItems.priceList:id,detail'
            ])
                ->select(
                    'id',
                    'booking_id',
                    'user_id',
                    'subcategory_id',
                    'slot_date',
                    'slot_start_time',
                    'slot_end_time',
                    'address_id',
                    'payment_method',
                    'created_at'
                )
                ->where('id', $order_id)
                ->first();

            if (!$booking) {
                return $this->errorResponse('Booking not found', 404);
            }

            $booking->orderItems->transform(function ($item) {

                if ($item->extra_service == 1) {
                    $item->makeHidden('service'); // hide service if extra
                } else {
                    $item->makeHidden('priceList'); // hide price list if not extra
                }

                $item->quantity = (string) $item->quantity;
                $item->unit_price = (string) $item->unit_price;
                $item->total_price = (string) $item->total_price;

                return $item;
            });

            $total = $booking->orderItems->sum(fn($item) => (float) $item->total_price);
            $booking->total_order_items_price = (string) $total;

            $logoPath = public_path('admin/img/logo.png');
            $logo = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
            $logo = Cache::remember('logo_base64', now()->addDay(), function () use ($logoPath) {
                return base64_encode(file_get_contents($logoPath));
            });


            $filename = 'invoice-' . $booking->booking_id . '-' . date('Ymd') . '.pdf';

            $pdf = Pdf::loadView('Admin.invoice.provider-invoice', [
                'booking' => $booking,
                'total' => $total,
                'logo' => $logo
            ])->setPaper('A4')->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);
            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "inline; filename=\"$filename\"");
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    // protected function added_services(Request $request)
    // {
    //     $validation = $this->validateRequest($request, [
    //         'order_id' => 'required|exists:orders,id',
    //     ]);

    //     if ($validation) return $validation;
    //     try {

    //         $items = OrderItem::where('order_id', $request->order_id)
    //             ->where('extra_service', 1)
    //             ->with(['priceList:id,detail'])
    //             ->select('id', 'order_id', 'service_id', 'quantity', 'total_price')
    //             ->get();

    //         return $this->successResponse($items, 'Added services retrieved successfully');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    protected function terms_conditions(Request $request)
    {
        
        $validation = $this->validateRequest($request, [
            'category_id' => 'required',
            'language' => 'required|in:en,hi'
        ]);

        if ($validation) return $validation;
        

        try {
            
                
            $category_id = $request->category_id;
            $language = $request->language;
            
            $terms_conditions = TermCondition::where('category_id', $category_id)->first();

        if (!$terms_conditions) {
            return $this->errorResponse('No terms condition found', 404);
        }

        $content = ($language === 'en') ? $terms_conditions->content_english : $terms_conditions->content_hindi;
            

            return $this->successResponse(['content' => $content], 'Terms Condition retrieved successfully');
            
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function upload_contacts(Request $request)
    {
        $validation = $this->validateRequest($request, [
        'contacts' => 'required|array',
        'contacts.*.name' => 'required|string|max:255',
        'contacts.*.number' => 'required|string|max:20',
    ]);

    if ($validation) return $validation;

    try {
        $userId = $this->user->id;

        // Step 1: Collect all numbers from request
        $incoming = collect($request->contacts)
            ->map(function ($item) {
                return [
                    'name' => $item['name'],
                    'phone' => trim($item['number']),
                ];
            })
            ->unique('phone') // remove duplicates inside the array
            ->values();

        $numbers = $incoming->pluck('phone');

        // Step 2: Get existing numbers for this user from DB
        $existing = Contact::where('user_id', $userId)
            ->whereIn('phone', $numbers)
            ->pluck('phone')
            ->toArray();

        // Step 3: Filter only new contacts
        $newContacts = $incoming->reject(function ($contact) use ($existing) {
            return in_array($contact['phone'], $existing);
        })->map(function ($contact) use ($userId) {
            return [
                'user_id' => $userId,
                'name' => $contact['name'],
                'phone' => $contact['phone'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->values();

        // Step 4: Bulk insert (only if we have data)
        if ($newContacts->isNotEmpty()) {
            Contact::insert($newContacts->toArray());
        }

            return $this->successResponse('Contacts saved');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
        protected function initiate(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'order_id' => 'required|string|max:63',
            'amount' => 'required|integer|min:100',
            'redirect_url' => 'required|url',
        ]);

        if ($validation)
            return $validation;

        try {
            $orderId = $request->input('order_id');
            $amount = $request->input('amount');
            $redirectUrl = $request->input('redirect_url');

            $authResponse = $this->phonePe->getAuthToken();

            if (!isset($authResponse['access_token'])) {
                return $this->errorResponse('Failed to generate PhonePe token', 500);
            }

            $accessToken = $authResponse['access_token'];

            $paymentResponse = $this->phonePe->createPaymentOrder(
                $accessToken,
                $orderId,
                $amount,
                $redirectUrl
            );
            if (isset($paymentResponse['code']) && $paymentResponse['code'] !== 'PAYMENT_INITIATED') {
                return $this->errorResponse('Failed to initiate payment', 500, $paymentResponse);
            }

            return $this->successResponse('Payment initiated', $paymentResponse);
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }

    }
    protected function handleCallback(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('PhonePe Callback Data', $data);

            $orderId = $request->input('merchantTransactionId');
            $status = $request->input('status');
            $transactionId = $request->input('transactionId');

            if (!$orderId || !$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid callback data.',
                ], 400);
            }

            switch (strtoupper($status)) {
                case 'SUCCESS':
                    return response()->json([
                        'success' => true,
                        'status' => 'SUCCESS',
                        'message' => 'Payment successful.',
                        'order_id' => $orderId,
                        'transaction_id' => $transactionId,
                    ]);

                case 'PENDING':
                    return response()->json([
                        'success' => false,
                        'status' => 'PENDING',
                        'message' => 'Payment is pending. Please verify after some time.',
                        'order_id' => $orderId,
                    ]);

                case 'FAILED':
                default:
                    return response()->json([
                        'success' => false,
                        'status' => 'FAILED',
                        'message' => 'Payment failed or was cancelled.',
                        'order_id' => $orderId,
                        'transaction_id' => $transactionId,
                    ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 'ERROR',
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        protected function paint_category(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($validation)
            return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $paint_categories = Part::where('category_id', $request->category_id)->get();

            if (!$paint_categories) {
                return $this->errorResponse('Invalid category selected', 400);
            }

            return $this->successResponse($paint_categories, 'Paint Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function paint_name(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'paint_category' => 'required|exists:parts,id'
        ]);

        if ($validation)
            return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $paint_names = PriceList::where('part_id', $request->paint_category)->get();

            if (!$paint_names) {
                return $this->errorResponse('Invalid paint category selected', 400);
            }

            return $this->successResponse($paint_names, 'Paint Name retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
}
