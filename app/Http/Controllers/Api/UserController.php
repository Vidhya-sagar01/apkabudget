<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\Service;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Zone;
use App\Models\ZoneProvider;
use App\Models\ServiceVideo;
use App\Models\Banner;
use App\Models\Part;
use App\Models\PriceList;
use App\Models\Category;
use App\Jobs\ProcessOrderPostCheckout;
use App\Models\Subscription;
use App\Models\ZoneRoundRobinTracker;
use App\Models\Complaint;
use App\Models\TermCondition;
use App\Models\HowItWork;

class UserController extends Controller
{
    protected function send_notification(Request $request)
    {

        $title = $request->title ?? "Test Notification";
        $message = $request->message ?? "This is a test message";

        // Fixing the undefined variable
        $tokens = User::whereIn('id', [20, 39])->pluck('device_token')->toArray();

        $response = $this->notificationService->sendPushNotification($tokens, $title, $message);

        dd($response);
    }
    protected function sub_categories(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'category_id'     => 'required|exists:categories,id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $category_id = $request->category_id;
            $SubCategories = SubCategory::where('category_id', $category_id)->select('id', 'name', 'image','details')->get();

            if ($SubCategories->isEmpty()) {
                return $this->errorResponse('No subcategory found', 404);
            }

            $SubCategories->transform(function ($subcategory) {
                $subcategory->image = url($subcategory->image);
                return $subcategory;
            });

            return $this->successResponse($SubCategories, 'Subctegories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function services(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'category_id'     => 'required|exists:categories,id',
            'subcategory_id'     => 'required|exists:sub_categories,id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id = $this->user->id;
            $category_id = $request->category_id;
            $subcategory_id = $request->subcategory_id;

            $SubSubCategories = SubSubCategory::where(['sub_subcategory_id' => $category_id, 'subcategory_id' => $subcategory_id])->select('id', 'sub_subcategory_name', 'image')->get();

            if ($SubSubCategories->isEmpty()) {
                return $this->errorResponse('No sub-subcategories found', 404);
            }
            $SubSubCategories->transform(function ($subSubCategory) {
                $subSubCategory->image = url($subSubCategory->image);
                return $subSubCategory;
            });
            $services = Service::where([
                'category_id'    => $category_id,
                'subcategory_id' => $subcategory_id
            ])->with('subSubCategory:id,sub_subcategory_name')
                ->select('id', 'sub_subcategory_id', 'service_name', 'image', 'price', 'time','details')
                ->get();

            if ($services->isEmpty()) {
                return $this->errorResponse('No service found', 404);
            }

            $cartItems = Cart::where('user_id', $user_id)
                ->whereIn('service_id', $services->pluck('id')) // Filter services in this subcategory
                ->with('service:id,service_name,image') // Load service details
                ->get()
                ->keyBy('service_id');

            $subcategory_total_price = $cartItems->sum('price');
            $total_items = $cartItems->sum('quantity');
            $total_unit_price = $cartItems->sum('unit_price');

            $groupedServices = $services->groupBy('sub_subcategory_id')->map(function ($services, $sub_subcategory_id) use ($cartItems) {
                return [
                    'sub_subcategory_id'   => $sub_subcategory_id,
                    'sub_subcategory_name' => $services->first()->subSubCategory->sub_subcategory_name ?? 'N/A',
                    'services'             => $services->map(function ($service) use ($cartItems) {
                        $cartItem = $cartItems[$service->id] ?? null;
                        return [
                            'id'           => $service->id,
                            'service_name' => $service->service_name,
                            'image'        => url($service->image),
                            'price'        => $service->price,
                            'time'         => $service->time,
                            'in_cart'      => $cartItem !== null,
                            'quantity'     => $cartItem->quantity ?? 0,
                            'total_price'  => $cartItem->price ?? 0, // Individual total
                            'unit_total_price' => ($cartItem ? $cartItem->unit_price * $cartItem->quantity : 0), // unit_price * quantity
                            'details' => collect(json_decode($service->details) ?? [])
                            ->map(function ($item, $index) {
                                return [
                                    'id'    => $index + 1,
                                    'title' => $item
                                ];
                            })->values()
                        ];
                    })
                ];
            })->values();

            // Add cart details related to this category/subcategory
            $cartDetails = $cartItems->map(function ($cartItem) {
                return [
                    'subcategory_id'   => $cartItem->subcategory_id,
                    'service_id'   => $cartItem->service->id,
                    'service_name' => $cartItem->service->service_name,
                    'image'        => url($cartItem->service->image),
                    'unit_price'   => $cartItem->unit_price,
                    'quantity'     => $cartItem->quantity,
                    'total_price'  => $cartItem->price
                ];
            })->values();

            $data = [
                'sub_subcategories' => $SubSubCategories,
                'services'                => $groupedServices,
                'cart_items'              => $cartDetails,
                'subcategory_total_price' => $subcategory_total_price,
                'total_items'             => $total_items,
                'total_unit_price'        => $total_unit_price,
            ];

            return $this->successResponse($data, 'Services retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function how_it_work(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'service_id' => 'required|exists:services,id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $how_it_works = HowItWork::where('service_id', $request->service_id)->get();

            if ($how_it_works->isEmpty()) {
                return $this->errorResponse('No How to work found', 404);
            }

            $how_it_works->transform(function ($how_it_work) {
                $how_it_work->image = url($how_it_work->image);
                return $how_it_work;
            });

            return $this->successResponse($how_it_works, 'How it work submitted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function rate_card(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'service_id'     => 'required|exists:services,id',
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id = $this->user->id;
            $service_id = $request->service_id;

            $parts = Part::where('service_id', $service_id)
                ->select('id', 'service_id', 'part')
                ->with(['priceLists' => function ($query) {
                    $query->select('id', 'part_id', 'detail', 'charge', 'labour_charge');
                }])
                ->get();

            if ($parts->isEmpty()) {
                return $this->errorResponse('No parts found for the given service', 404);
            }

            $data = $parts->map(function ($part) {
                return [
                    'part_id' => $part->id,
                    'part_name' => $part->part,
                    'prices' => $part->priceLists->map(function ($price) {
                        return [
                            'price_id' => $price->id,
                            'detail' => $price->detail,
                            'charge' => $price->charge,
                            'labour_charge' => $price->labour_charge
                        ];
                    }),
                ];
            });

            return $this->successResponse($data, 'Rate card retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function addToCart(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'service_id'     => 'required|exists:services,id',
            'quantity'   => 'required|integer|min:0'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id    = $this->user->id;
            $service_id = $request->service_id;
            $quantity   = $request->quantity;

            $service = Service::find($service_id);
            if (!$service) {
                return $this->errorResponse('Service not found', 404);
            }
            $subcategory_id = $service->subcategory_id;

            $cartItem = Cart::where(['user_id' => $user_id, 'service_id' => $service_id])->first();

            if ($cartItem) {
                if ($quantity == 0) {
                    // Remove item if quantity is 0
                    $cartItem->delete();
                    return $this->successResponse([], 'Service removed from cart');
                }

                // Update quantity
                $cartItem->quantity = $quantity;
                $cartItem->unit_price = $cartItem->unit_price ?? $service->price; // Ensure stored price
                $cartItem->price = $cartItem->unit_price * $quantity;
                $cartItem->save();
            } else {
                if ($quantity > 0) {
                    // Create new cart item only if quantity > 0
                    Cart::create([
                        'subcategory_id'  => $subcategory_id,
                        'user_id'    => $user_id,
                        'service_id' => $service_id,
                        'quantity'   => $quantity,
                        'unit_price' => $service->price,
                        'price'      => $service->price * $quantity
                    ]);
                } else {
                    return $this->errorResponse('Invalid quantity', 400);
                }
            }

            return $this->successResponse([], 'Service added to cart successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    // protected function view_cart()
    // {
    //     try {
    //         if (!$this->user) {
    //             return $this->errorResponse('User not found', 404);
    //         }

    //         $user_id    = $this->user->id;
    //         $cartItems = Cart::where('user_id', $user_id)->with('service:id,service_name,image')->get();

    //         if ($cartItems->isEmpty()) {
    //             return $this->errorResponse('Cart is empty', 404);
    //         }

    //         $total_price = 0;
    //         $total_items = 0;

    //         $cartDetails = $cartItems->map(function ($cartItem) use (&$total_price, &$total_items) {
    //             $service = $cartItem->service;
    //             if (!$service) return null; // Skip if service is missing

    //             $total_price += $cartItem->price; // Use stored price
    //             $total_items += $cartItem->quantity;

    //             return [
    //                 'service_id'   => $service->id,
    //                 'service_name' => $service->service_name,
    //                 'image'        => url($service->image),
    //                 'unit_price'   => $cartItem->unit_price, // Show stored price
    //                 'quantity'     => $cartItem->quantity,
    //                 'total_price'  => $cartItem->price // Use stored total price
    //             ];
    //         })->filter()->values();

    //         $calculation = [
    //             'total_items' => $total_items,
    //             'total_price' => $total_price,
    //             'cart_items'  => $cartDetails
    //         ];

    //         return $this->successResponse($calculation, 'Cart retrieved successfully');
    //     } catch (\Exception $e) {
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }
    
    protected function save_location(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'address'     => 'required',
            'latitude'     => 'required',
            'longitude'     => 'required',
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $this->user->address = $request->address;
            $this->user->latitude = $request->latitude;
            $this->user->longitude = $request->longitude;
            $this->user->save();

            return $this->successResponse('Location saved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function add_address(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'type'     => 'required', //1-home , 2-other
            'address'     => 'required',
            'latitude'   => 'required',
            'longitude'   => 'required',
            'flat_no'   => 'required'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id    = $this->user->id;
            $type = $request->type;
            $address = $request->address;
            $latitude   = $request->latitude;
            $longitude   = $request->longitude;
            $flat_no   = $request->flat_no;
            $landmark   = $request->landmark;

            $newAddress = Address::create([
                'type'      => $type,
                'user_id'      => $user_id,
                'address' => $address,
                'latitude'     => $latitude,
                'longitude'    => $longitude,
                'flat_no'      => $flat_no,
                'landmark'     => $landmark
            ]);

            return $this->successResponse([], 'Address added successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function addresses()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id    = $this->user->id;
            $addresses = Address::where('user_id', $user_id)->select('id', 'type', 'user_id', 'address', 'latitude', 'longitude', 'flat_no', 'landmark')->get();

            return $this->successResponse($addresses, 'Address retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function getDailySlots(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'date' => 'required|date' // YYYY-MM-DD format
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $date = Carbon::parse($request->date)->setTimezone('Asia/Kolkata'); // Local timezone
            $startTime = $date->copy()->setTime(9, 0); // 9:00 AM
            $endTime = $date->copy()->setTime(21, 0);  // 9:00 PM
            $interval = 15; // 15-minute slots
            $slots = [];

            $now = Carbon::now('Asia/Kolkata'); // Current time in IST
            $threshold = $now->copy()->addHours(2); // 2 hours from now

            while ($startTime < $endTime) {
                $nextSlot = $startTime->copy()->addMinutes($interval);

                // Skip past slots if date is today
                if ($date->isToday() && $startTime->lessThan($threshold)) {
                    $startTime = $nextSlot;
                    continue;
                }

                $slots[] = [
                    'slot' => $startTime->format('g:i A') . ' - ' . $nextSlot->format('g:i A'),
                    'start_time' => $startTime->format('H:i'),
                    'end_time' => $nextSlot->format('H:i'),
                    'is_available' => true
                ];

                $startTime = $nextSlot;
            }

            return $this->successResponse([
                'date' => $date->toDateString(),
                'slots' => $slots
            ], 'Slots fetched successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function checkout(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'subcategory_id'     => 'required|exists:sub_categories,id',
            'address_id' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:online,cod',
            'slot_date' => 'required|date_format:Y-m-d|after_or_equal:today', //2025-03-11
            'slot_start_time' => 'required|date_format:H:i', // Format: 09:00
            'slot_end_time' => 'required|date_format:H:i|after:slot_start_time' // Ensure end time is after start time
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id = $this->user->id;
            $subcategory_id = $request->subcategory_id;
            $address_id = $request->address_id;
            $payment_method = $request->payment_method;
            $slot_date = $request->slot_date;
            $slot_start_time = $request->slot_start_time;
            $slot_end_time = $request->slot_end_time;

            $address = Address::find($address_id);
            if (!$address) {
                return $this->errorResponse('Address not found', 404);
            }

            $latitude  = (float) $address->latitude;
            $longitude = (float) $address->longitude;

            $zones = Zone::all(['id', 'boundary']);
            $matchedZone = null;

            foreach ($zones as $zone) {
                $boundaries = json_decode($zone->boundary, true);
                if (!is_array($boundaries)) {
                    throw new \Exception("Invalid boundary data for zone: " . $zone->id);
                }
                if ($this->isPointInPolygon($latitude, $longitude, $boundaries)) {
                    $matchedZone = $zone;
                    break;
                }
            }

            // if (!$matchedZone) {
            //     return $this->errorResponse('No zone found for this address', 400);
            // }

            $zone_id = $matchedZone ? $matchedZone->id : null;

            // Fetch cart items for this subcategory
            $cartItems = Cart::where('user_id', $user_id)
                ->where('subcategory_id', $subcategory_id)
                // ->whereNull('deleted_at')
                ->get();

            if ($cartItems->isEmpty()) {
                return $this->errorResponse('Cart is empty for this subcategory', 400);
            }

            $total_price = $cartItems->sum('price');
            // $booking_id = 'BOOK-' . strtoupper(Str::random(8));
            // $orderStatus = $payment_method === 'cod' ? 'placed' : 'pending';
            
            $serviceIds = $cartItems->pluck('service_id');

            $categoryIds = Service::whereIn('id', $serviceIds)
                ->pluck('category_id')
                ->unique();
            
            $maxPrice = Category::whereIn('id', $categoryIds)->max('max_price');
            
            if ($total_price < $maxPrice) {
                return $this->errorResponse("Total price ₹{$total_price} is less than the minimum required price ₹{$maxPrice} for this category.", 400);
            }

            Order::where('user_id', $user_id)
                ->where('subcategory_id', $subcategory_id)
                ->where('status', 'pending')
                ->delete();

            // Check for existing pending order
            // $order = Order::firstOrCreate(
            //     [
            //         'user_id' => $user_id,
            //         'subcategory_id' => $subcategory_id,
            //         'status' => $orderStatus
            //     ],
            //     [
            //         'address_id' => $address_id,
            //         'total_price' => $total_price,
            //         'payment_method' => $payment_method,
            //         'booking_id' => $booking_id,
            //         'slot_date' => $slot_date,
            //         'slot_start_time' => $slot_start_time,
            //         'slot_end_time' => $slot_end_time
            //     ]
            // );
            $order = Order::create([
                'user_id' => $user_id,
                'subcategory_id' => $subcategory_id,
                'address_id' => $address_id,
                'zone_id' => $zone_id,
                'total_price' => $total_price,
                'payment_method' => $payment_method,
                // 'booking_id' => $booking_id,
                'slot_date' => $slot_date,
                'slot_start_time' => $slot_start_time,
                'slot_end_time' => $slot_end_time,
                'status' => $payment_method == 'cod' ? 'placed' : 'pending'
            ]);
            $bookingId = 'BOOK-' . str_pad($order->id, 4, '0', STR_PAD_LEFT);
            $order->update(['booking_id' => $bookingId]);

            OrderItem::where('order_id', $order->id)->delete();

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'service_id' => $cartItem->service_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'total_price' => $cartItem->price
                ]);
            }
            //cart item delete
            Cart::where('user_id', $user_id)->where('subcategory_id', $subcategory_id)->update(['deleted_at' => now()]);
            // if ($payment_method == 'cod') {
            //                 ProcessOrderPostCheckout::dispatch($order, $user_id, $subcategory_id);
            //             }
            // if ($payment_method == 'cod') {
            //     $zoneProviderIds = ZoneProvider::where('zone_id', $zone_id)->pluck('user_id');
            //     if ($zoneProviderIds->isNotEmpty()) {

            //         $serviceProviders = User::whereIn('id', $zoneProviderIds)
            //             ->where('role', 2)
            //             ->whereNotNull('device_token')
            //             ->pluck('device_token')
            //             ->toArray();

            //         $title = 'New Booking Received!';
            //         $message = "You have received a new booking (ID: {$bookingId}). Total Amount: ₹{$total_price}.";

            //         foreach ($zoneProviderIds as $providerId) {
            //             Notification::create([
            //                 'user_id' => $providerId,
            //                 'title'   => $title,
            //                 'message' => $message
            //             ]);
            //         }

            //         if (!empty($serviceProviders)) {
            //             $this->notificationService->sendPushNotification($serviceProviders, $title, $message);
            //         }
            //     }

            //     if (!empty($this->user->device_token)) {
            //         $userTitle = 'Booking Placed Successfully!';
            //         $userMessage = "Your booking (ID: {$bookingId}) has been placed successfully. Total Amount: ₹{$total_price}. Our providers will reach out soon.";

            //         Notification::create([
            //             'user_id' => $this->user->id,
            //             'title'   => $userTitle,
            //             'message' => $userMessage
            //         ]);

            //         $this->notificationService->sendPushNotification([$this->user->device_token], $userTitle, $userMessage);
            //     }
            //     Cart::where('user_id', $user_id)->where('subcategory_id', $subcategory_id)->forceDelete();
            // }
            if ($payment_method == 'cod') {
                $zone = Zone::find($zone_id);
                
                $subcategory = SubCategory::find($subcategory_id);
                if (!$subcategory) {
                    return $this->errorResponse('Subcategory not found', 404);
                }
                $categoryId = $subcategory->category_id;

                $providers = $zone->providers()
                    ->where('category_id', $categoryId)
                    ->orderBy('created_at')
                    ->get();

                // $providers = $zone->providers()->orderBy('created_at')->get();

                $eligibleProviders = $providers->filter(function ($provider) {
                    return Subscription::getActiveSubscription($provider->id, 1) &&
                        Subscription::getActiveSubscription($provider->id, 2);
                });

                $providerIds = $eligibleProviders->pluck('id')->toArray();

                if (!empty($providerIds)) {
                    $tracker = ZoneRoundRobinTracker::firstOrCreate(
                        ['zone_id' => $zone->id],
                        ['last_assigned_user_id' => null]
                    );

                    $lastIndex = $tracker->last_assigned_user_id
                        ? array_search($tracker->last_assigned_user_id, $providerIds)
                        : -1;

                    $nextIndex = ($lastIndex + 1) % count($providerIds);
                    $nextProviderId = $providerIds[$nextIndex];

                    $order->update(['provider_id' => $nextProviderId]);

                    $tracker->update(['last_assigned_user_id' => $nextProviderId]);

                    $provider = $eligibleProviders->where('id', $nextProviderId)->first();
                    $title = 'New Booking Assigned to You!';
                    $message = "Booking (ID: {$bookingId}) has been assigned to you. Total Amount: ₹{$total_price}.";

                    Notification::create([
                        'user_id' => $nextProviderId,
                        'title'   => $title,
                        'message' => $message
                    ]);

                    $this->notificationService->sendPushNotification([$provider->device_token], $title, $message);
                }

                if (!empty($this->user->device_token)) {
                    $userTitle = 'Booking Placed Successfully!';
                    $userMessage = "Your booking (ID: {$bookingId}) has been placed successfully. Total Amount: ₹{$total_price}. Our providers will reach out soon.";

                    Notification::create([
                        'user_id' => $this->user->id,
                        'title'   => $userTitle,
                        'message' => $userMessage
                    ]);

                    $this->notificationService->sendPushNotification([$this->user->device_token], $userTitle, $userMessage);
                }
                Cart::where('user_id', $user_id)->where('subcategory_id', $subcategory_id)->forceDelete();
            }
            $Responsedata = [
                'order_id'    => $order->id,
                'booking_id'  => $bookingId,
                'total_price' => $total_price,
                'status'      => $order->status
            ];

            return $this->successResponse($Responsedata, 'Checkout successful, proceed to payment');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
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
    
    protected function paymentstatus(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'order_id' => 'required|exists:orders,id',
            'payment_status' => 'required|in:success,failed',
            'transaction_id'  => 'nullable|string'
        ]);

        if ($validation) return $validation;

        try {

            $order = Order::find($request->order_id);

            $transaction_id = $request->transaction_id ?? 'TXN-' . strtoupper(Str::random(10));

            Transaction::create([
                'type' => 3,
                'user_id' => $this->user->id,
                'order_id' => $order->id,
                'transaction' => 2,
                'amount' => $order->total_price,
                'transaction_id' => $transaction_id,
                'status' => $request->payment_status
            ]);

            if ($request->payment_status == 'success') {
                $order->status = 'completed';
                Cart::where('user_id', $this->user->id)->where('subcategory_id', $order->subcategory_id)->forceDelete();

                $serviceProviders = User::where('role', 2)->whereNotNull('device_token')->pluck('device_token')->toArray();
                // Save notification for each provider
                foreach ($serviceProviders as $token) {
                    Notification::create([
                        'user_id' => User::where('device_token', $token)->value('id'),
                        'title'   => 'New Booking Received!',
                        'message' => "You have received a new booking (ID: {$order->booking_id}). Total Amount: ₹{$order->total_price}."
                    ]);
                }

                // Send push notification to multiple providers
                if (!empty($serviceProviders)) {
                    $title = 'New Booking Received!';
                    $message = "Booking ID: {$order->booking_id}, Amount: ₹{$order->total_price}.";
                    $this->notificationService->sendPushNotification($serviceProviders, $title, $message);
                }
            } else {
                $order->status = 'failed';
                Cart::where('user_id', $this->user->id)->where('subcategory_id', $order->subcategory_id)->restore();
            }
            $order->transaction_id = $transaction_id;
            $order->save();

            $Responsedata = [
                'order_id'        => $order->id,
                'booking_id'      => $order->booking_id,
                'transaction_id'  => $transaction_id,
                'status'          => $order->status
            ];
            return $this->successResponse($Responsedata, $request->payment_status == 'success' ? 'Payment successful, order confirmed' : 'Payment failed, please try again');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function my_bookings()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id = $this->user->id;

            $bookings = Order::with([
                'provider:id,name,mobile_no',
                'subCategory:id,name,image',
                'address:id,address,latitude,longitude,flat_no,landmark',
                'orderItems:id,order_id,service_id,quantity,unit_price,total_price',
                'orderItems.service:id,service_name,image'
            ])
                ->select('id', 'provider_id', 'booking_id', 'subcategory_id', 'slot_date', 'slot_start_time', 'slot_end_time', 'address_id', 'total_price', 'payment_method', 'status', 'created_at','completed_date')
                ->where('user_id', $user_id)
                ->orderBy('id', 'DESC')
                ->get();

            $bookings->transform(function ($booking) {
                // Subcategory image
                if ($booking->subCategory && $booking->subCategory->image) {
                    $booking->subCategory->image = url($booking->subCategory->image);
                }

                // Each order item service image
                if ($booking->orderItems) {
                    foreach ($booking->orderItems as $item) {
                        if ($item->service && $item->service->image) {
                            $item->service->image = url($item->service->image);
                        }
                    }
                }
                
                $canComplain = false;
    if ($booking->status === 'completed') {
        $baseDate = $booking->completed_date 
            ? Carbon::parse($booking->completed_date)
            : Carbon::parse($booking->created_at);
        
        $canComplain = now()->diffInMonths($baseDate) < 3;
    }

    $booking->can_complain = $canComplain;

                return $booking;
            });


            if ($bookings->isEmpty()) {
                return $this->errorResponse('No booking found', 404);
            }

            return $this->successResponse($bookings, 'Booking retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function submitComplaint(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'order_id' => 'required|exists:orders,id',
            'message' => 'required|string|max:1000'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $user_id = $this->user->id;
            $order = Order::find($request->order_id);

            if ($order->user_id !== $user_id) {
                return $this->errorResponse('Unauthorized complaint', 403);
            }

            $existingComplaint = Complaint::where('order_id', $order->id)
                ->where('user_id', $user_id)
                ->where('status', '!=', 'resolved')
                ->first();

            if ($existingComplaint) {
                return $this->successResponse('Complaint already submitted for this order and is not yet resolved.');
            }

            $complaint = Complaint::create([
                'user_id' => $user_id,
                'provider_id' => $order->provider_id,
                'order_id' => $order->id,
                'message' => $request->message,
            ]);

            $provider = User::find($order->provider_id);
            $title = 'New Complaint Received';
            $message = "You have received a new complaint for Booking ID: {$order->booking_id}.";

            Notification::create([
                'user_id' => $provider->id,
                'title'   => $title,
                'message' => $message,
            ]);

            if ($provider->device_token) {
                $this->notificationService->sendPushNotification([$provider->device_token], $title, $message);
            }

            return $this->successResponse('Complaint submitted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function service_videos()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $service_videos = ServiceVideo::select('id', 'video_url')->orderBy('id', 'DESC')->get();

            $service_videos->transform(function ($video) {
                if ($video->video_url) {
                    $video->video_url = url($video->video_url);
                }
                return $video;
            });

            if ($service_videos->isEmpty()) {
                return $this->errorResponse('No service videos found', 404);
            }

            return $this->successResponse($service_videos, 'Service Videos retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function banners()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $banners = Banner::select('id', 'image')->orderBy('id', 'DESC')->get();

            $banners->transform(function ($banner) {
                if ($banner->image) {
                    $banner->image = url($banner->image);
                }
                return $banner;
            });

            if ($banners->isEmpty()) {
                return $this->errorResponse('No Banner found', 404);
            }

            return $this->successResponse($banners, 'Banner retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function price_list(Request $request)
    {
        $validation = $this->validateRequest($request, [
            'category_id'     => 'required|exists:categories,id',
            'order_id' => 'nullable|exists:orders,id'
        ]);

        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $category_id = $request->category_id;
            $order_id = $request->order_id;

            $parts = Part::with(['priceLists:id,part_id,detail,charge,labour_charge'])->where('category_id', $category_id)->select('id', 'category_id', 'part')->get();
            $quantities = [];

            if ($order_id) {
                $quantities = OrderItem::where('order_id', $order_id)
                    ->where('extra_service', 1)
                    ->get(['service_id', 'quantity'])
                    ->keyBy('service_id');
            }

            $parts->each(function ($part) use ($quantities) {
                $part->priceLists->each(function ($price) use ($quantities) {
                    $price->qty = isset($quantities[$price->id]) ? $quantities[$price->id]->quantity : "0";
                });
            });

            if ($parts->isEmpty()) {
                return $this->errorResponse('No part found', 404);
            }

            return $this->successResponse($parts, 'Price retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
    protected function getTrendingServicesByCategory(int $days = 7, int $limit = 3)
    {
        $categories = Category::select('id', 'category')->get();

        $result = $categories->map(function ($category) use ($days, $limit) {
            $adminTrendingServices = Service::where('category_id', $category->id)
                ->where('is_trending', true)
                ->select('id', 'service_name', 'image', 'price', 'time')
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'service_name' => $service->service_name,
                        'image' => $service->image ? url($service->image) : null,
                        'price' => $service->price,
                        'time' => $service->time,
                    ];
                })->values();

            if ($adminTrendingServices->isEmpty()) {
                $trendingOrderItems = OrderItem::join('services', 'order_items.service_id', '=', 'services.id')
                    ->where('services.category_id', $category->id)
                    ->where('order_items.created_at', '>=', now()->subDays($days))
                    ->select('order_items.service_id')
                    ->selectRaw('SUM(order_items.quantity) as total_quantity')
                    ->groupBy('order_items.service_id')
                    ->orderByDesc('total_quantity')
                    ->get();

                $autoTrending = $trendingOrderItems->map(function ($item) {
                    $service = Service::find($item->service_id);
                    if (!$service) return null;
                    return [
                        'id' => $service->id,
                        'service_name' => $service->service_name,
                        'image' => $service->image ? url($service->image) : null,
                        'price' => $service->price,
                        'time' => $service->time,
                    ];
                })->filter()
                    ->unique('service_name')
                    ->values()
                    ->take($limit);
                if ($autoTrending->isNotEmpty()) {
                    return [
                        'category_id' => $category->id,
                        'category_name' => $category->category,
                        'trending_services' => $autoTrending,
                    ];
                }
            } else {
                return [
                    'category_id' => $category->id,
                    'category_name' => $category->category,
                    'trending_services' => $adminTrendingServices->take($limit),
                ];
            }
            return null;
        })->filter()->values()->all();

        return $result;
    }

    protected function home()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User  not found', 404);
            }

            $banners = [];

            foreach ([1, 2, 3] as $type) {
                $banners["banner{$type}"] = Banner::select('id', 'image')
                    ->where('type', $type)
                    ->orderByDesc('id')
                    ->get()
                    ->transform(fn($b) => [
                        'id' => $b->id,
                        'image' => $b->image ? url($b->image) : null,
                    ]);
            }

            $newlaunchservices = Service::select('id', 'service_name', 'image', 'price', 'time')
                ->orderByDesc('id')
                ->limit(3)
                ->get()
                ->transform(fn($s) => [
                    'id' => $s->id,
                    'service_name' => $s->service_name,
                    'image' => $s->image ? url($s->image) : null,
                    'price' => $s->price,
                    'time' => $s->time,
                ]);

            $mostbooked = OrderItem::select('service_id')
                ->selectRaw('SUM(quantity) as total_quantity')
                ->groupBy('service_id')
                ->orderByDesc('total_quantity')
                ->get();

            $mostbookedservices = $mostbooked->map(function ($item) {
                $service = Service::find($item->service_id);
                if (!$service) return null;

                return [
                    'id' => $service->id,
                    'service_name' => $service->service_name,
                    'image' => $service->image ? url($service->image) : null,
                    'price' => $service->price,
                    'time' => $service->time,
                ];
            })->filter()
                ->unique('service_name')
                ->values()
                ->take(3);

            $trendingservicesByCategory = $this->getTrendingServicesByCategory();

            return $this->successResponse([
                'banners' => $banners,
                'newlaunchservices' => $newlaunchservices,
                'mostbookedservices' => $mostbookedservices,
                'trendingservices' => $trendingservicesByCategory,
            ], 'Home data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
     protected function user_terms_conditions()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $terms = TermCondition::where('type', 1)->first();

        if (!$terms) {
            return $this->errorResponse('No terms condition found', 404);
        }

        return $this->successResponse([
            'content' => $terms->content_english
            // 'content_hindi'   => $terms->content_hindi
        ], 'User Terms Condition retrieved successfully');
        
            // return $this->successResponse($terms, 'User Terms Condition retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    
}
