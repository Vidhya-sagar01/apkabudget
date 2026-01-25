<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SubSubCategory;
use App\Models\Notification;
use App\Models\Transaction;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\User;
use App\Models\PrivacyPolicy;
use App\Models\RefundPolicy;
use App\Models\AboutUs;
use App\Models\ContactUs;
use App\Models\SocialProfile;

class CommonController extends Controller
{
    
   protected function test_api(Request $request)
    {   
        $response = Array();
        $provider_id = $request->provider_id;
        $title = $request->title;
        $message = $request->message;
        $device_token = $request->device_token;
        $providerInfo = User::where('id', $provider_id)->where('role', 2)->first();
        if(!isset($title) || empty($title)){
             $title = 'New Booking Received!'; 
        }
        if(!isset($message) || empty($message)){
           $message = "You have received a new booking (ID: Book-2045) Total Amount: ₹500.";
        }
       
        $deviceTokens = Array();
        
        if(isset($device_token) && !empty($device_token)){
            $deviceTokens[] = $device_token;
        }
        $deviceTokens[] = $providerInfo->device_token;
       
        // dd($device_token);

        $response['provider_id'] = $provider_id;
        $response['device_token'] = $device_token;
        $response['title'] = $title;
        $response['message'] = $message;
        $response['device_tokens'] = $deviceTokens;
        $response['providerInfo'] = $providerInfo;
        $response['notificationService'] = $this->notificationService;
        
        // Test push notification
        if (!empty($deviceTokens)) {
            $notification = array(
                'title' => $title,
                'body'  => $message,
                'image' => "" // This can be https URL
            );
            $appData = [
                "id" => "2670",
                "booking_id" => "BOOK-2670",
                "user_id" => "3129",
                "subcategory_id" => "26",
                "address_id" => "1410",
                "total_price" => "49",
                "status" => "placed",
                "provider_id" => "2370",
                "created_at" => "2025-09-18T06:06:33.000000Z",
                "slot_date" => "2025-09-18",
                "slot_start_time" => "13:45:00",
                "slot_end_time" => "14:00:00",
                "distance" => "",
                "duration" => "",
                "complaint_message" => "",
                "complaint_date" => "",
                "user" => "{\"id\":3129,\"name\":null,\"mobile_no\":\"7451064215\"}",
                "sub_category" => "{\"id\":26,\"name\":\"Few walls & rooms\",\"image\":\"https:\/\/api.apkabudget.com\/uploads\/categories\/1758011532.png\",\"category_id\":13}",
                "address" => "{\"id\":1410,\"address\":\"Noida, Uttar Pradesh, India\",\"latitude\":\"28.5355161\",\"longitude\":\"77.3910265\",\"flat_no\":\"Noida UP\",\"landmark\":\"Noida\"}",
                "complaints" => "[]"
            ];
            $sound_name = 'incoming_tone';
            // $response['push_results'] = $this->notificationService->send($deviceTokens, $notification, $appData, $sound_name);
            
            $title = 'New Booking Received!';
            $message = "Booking ID: 12345, Amount: ₹123.";
            $this->notificationService->sendPushNotification($deviceTokens, $title, $message);
        } 
        
        
        return $this->successResponse($response, 'Testing push notification.');
    }

    protected function categories()
    {
        try {
            $categories = Category::where('status', 1)->select('id', 'category', 'image')->get();

            if ($categories->isEmpty()) {
                return $this->errorResponse('No categories found', 404);
            }

            $categories->transform(function ($category) {
                $category->image = url($category->image);
                return $category;
            });

            return $this->successResponse($categories, 'Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function subCategories()
    {
        try {
            $categories = SubCategory::where('status', 1)
                ->select('id', 'category_id', 'name', 'image')
                ->with(['category:id,name']) // load parent category name
                ->get();

            if ($categories->isEmpty()) {
                return $this->errorResponse('No categories found', 404);
            }

            $categories->transform(function ($category) {
                $category->image = url($category->image);
                return $category;
            });

            return $this->successResponse($categories, 'Sub Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function subSubCategories()
    {
        try {
            $categories = SubSubCategory::where('status', 1)
                ->select('id', 'subcategory_id', 'sub_subcategory_name', 'image')
                ->with(['subcategory:id,name,category_id']) // load parent subcategory
                ->get();

            if ($categories->isEmpty()) {
                return $this->errorResponse('No sub-sub categories found', 404);
            }

            $categories->transform(function ($category) {
                $category->image = url($category->image);
                return $category;
            });

            return $this->successResponse($categories, 'Sub Sub Categories retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    protected function notifications()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $notifications = Notification::where('user_id', $this->user->id)->orderBy('id', 'DESC')->get();

            $notifications = $notifications->map(function ($notification) {
                $notification->time_ago = Carbon::parse($notification->created_at)->diffForHumans();
                return $notification;
            });

            return $this->successResponse($notifications, 'Notifications retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function transaction_history()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $transactions = Transaction::where('user_id', $this->user->id)
                ->orderBy('id', 'DESC')
                ->get(['id', 'type', 'transaction', 'amount', 'transaction_id', 'status', 'created_at']);

            // Format created_at for human-readable display
            $formattedTransactions = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'transaction' => $transaction->transaction,
                    'amount' => $transaction->amount,
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->format('d M Y, h:i A'), // Proper formatting
                ];
            });

            return $this->successResponse($formattedTransactions, 'Transaaction retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function profile()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $userData = $this->user->role == 2
                ? array_merge(
                    $this->user->only(['id', 'name', 'mobile_no', 'email', 'category_id', 'experience', 'identity_id', 'identity_number', 'identity_image', 'country_id', 'state_id', 'city_id', 'pincode', 'address']),
                    [
                        'identity_image' => $this->user->identity_image ? url($this->user->identity_image) : null,
                        'identity_image_back' => $this->user->identity_image_back ? url($this->user->identity_image_back) : null,
                        'security_added' => Subscription::hasActiveSubscription($this->user->id, 2),
                        'plan_activity' => Subscription::hasActiveSubscription($this->user->id, 1),
                        'delivered_service' => 0
                    ]
                )
                : $this->user->only(['id', 'name', 'mobile_no', 'email']);


            return $this->successResponse($userData, 'Profile retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function edit_profile(Request $request)
    {
        $rules = $this->user->role == 2 ? [
            // 'name'            => 'required',
            // 'country_id'      => 'required',
            // 'state_id'        => 'required',
            // 'city_id'         => 'required',
            // 'pincode'         => 'required',
            // 'address'         => 'required',
            // 'category_id'     => 'required|exists:categories,id',
            'experience'      => 'required|integer|min:1',
            'identity_id'     => 'required|exists:identity_types,id',
            'identity_number' => 'required|string|max:50|unique:users,identity_number,' . auth()->id(),
            'identity_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'identity_image_back'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ] : [
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ];

        $validation = $this->validateRequest($request, $rules);
        if ($validation) return $validation;

        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            if ($request->hasFile('identity_image') && $this->user->role == 2) {
                $filename = 'identity_' . time() . '.' . $request->file('identity_image')->getClientOriginalExtension();
                $request->file('identity_image')->move(public_path('uploads/identities/'), $filename);
                $this->user->identity_image = 'uploads/identities/' . $filename;
            }
            
             if ($request->hasFile('identity_image_back') && $this->user->role == 2) {
                $filename = 'identity_back_' . time() . '.' . $request->file('identity_image_back')->getClientOriginalExtension();
                $request->file('identity_image_back')->move(public_path('uploads/identities/'), $filename);
                $this->user->identity_image_back = 'uploads/identities/' . $filename;
            }

            $fields = $this->user->role == 2
                ? ['name', 'country_id', 'state_id', 'city_id', 'pincode', 'address', 'experience', 'identity_id', 'identity_number']
                : ['name', 'email'];

            foreach ($fields as $field) {
                if ($request->has($field)) {
                    $this->user->$field = $request->$field;
                }
            }

            $this->user->save();

            return $this->successResponse('Profile updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    // protected function handleWebhook(Request $request)
    // {
    //     Log::info('Webhook Initiated');
    //     try {

    //         $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
    //         $signature = $request->header('X-Razorpay-Signature');
    //         $payload = $request->getContent();

    //         Log::info('Webhook Secret:', ['webhookSecret' => $webhookSecret]);
    //         Log::info('Received Signature:', ['signature' => $signature]);

    //         $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
    //         Log::info('Expected Signature:', ['expectedSignature' => $expectedSignature]);

    //         if (!hash_equals($expectedSignature, $signature)) {
    //             Log::error('Invalid webhook signature');
    //             return $this->errorResponse('Invalid signature', 403);
    //         }

    //         $payload = $request->all();
    //         Log::info('Payload',$payload);
    //         $event = $payload['event'] ?? null;
    //         $payment = $payload['payload']['payment']['entity'] ?? null;

    //         $userId = $payment['notes']['user_id'] ?? null;
    //         $planId = $payment['notes']['plan_id'] ?? null;
    //         $amount = $payment['amount'] ?? null; // in paise
    //         $transactionId = $payment['id'] ?? null;
    //         $paymentStatus = $payment['status'] ?? 'failed';
    //         $failureReason = $payment['error_description'] ?? 'N/A';

    //         Log::info('Extracted Data:', [
    //             'userId' => $userId,
    //             'planId' => $planId,
    //             'amount' => $amount,
    //             'transactionId' => $transactionId
    //         ]);

    //         if (!$userId || !$planId || !$transactionId || !$amount) {
    //             Log::error('Missing required fields', [
    //                 'userId' => $userId,
    //                 'planId' => $planId,
    //                 'transactionId' => $transactionId,
    //                 'amount' => $amount
    //             ]);
    //             return $this->errorResponse('User or plan not found', 404);
    //         }

    //         $plan = Plan::find($planId);
    //         $user = User::find($userId);

    //         if (!$plan || !$user) {
    //             Log::error('User or Plan not found', [
    //                 'planId' => $planId,
    //                 'userId' => $userId
    //             ]);
    //             return $this->errorResponse('User or plan not found', 404);
    //         }

    //         // Convert amount to INR (paise to rupees)
    //         $amountInRupees = $amount / 100;

    //         if ($event === 'payment.authorized') {
    //             $capture = $this->paymentService->capturePayment($transactionId, $amount);
    //             $status = $capture ? 'success' : 'failed';
    //         } elseif ($event === 'payment.failed') {
    //             $status = 'failed';
    //         } else {
    //             return $this->errorResponse('Unhandled event type', 400);
    //         }

    //         $startDate = ($status === 'success') ? now() : null;
    //         $endDate = ($status === 'success') ? now()->addDays($plan->duration) : null;

    //         $subscriptionStatus = ($status === 'success') ? 'active' : 'pending';

    //         $existingSubscription = Subscription::updateOrCreate(
    //             ['user_id' => $userId, 'type' => $plan->type],
    //             [
    //                 'plan_id' => $planId,
    //                 'status' => $subscriptionStatus,
    //                 'start_date' => $startDate,
    //                 'end_date' => $endDate
    //             ]
    //         );

    //         Log::info('Subscription processed', [
    //             'status' => $subscriptionStatus,
    //             'subscriptionId' => $existingSubscription->id
    //         ]);

    //         Transaction::create([
    //             'type' => $plan->type,
    //             'user_id' => $userId,
    //             'transaction' => 2, // debit
    //             'amount' => $amountInRupees,
    //             'transaction_id' => $transactionId,
    //             'subscription_id' => $existingSubscription->id,
    //             'status' => $status,
    //             'failure_reason' => $failureReason
    //         ]);

    //         Log::info('Transaction recorded');

    //         return $this->successResponse('Webhook processed successfully', $status);
    //     } catch (\Exception $e) {
    //         Log::error('Webhook processing failed', ['error' => $e->getMessage()]);
    //         return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
    //     }
    // }

    protected function handleWebhook(Request $request)
    {
        Log::info('Webhook Initiated');
        try {

            $webhookSecret = env('RAZORPAY_WEBHOOK_SECRET');
            $signature = $request->header('X-Razorpay-Signature');
            $payload = $request->getContent();

            Log::info('Webhook Secret:', ['webhookSecret' => $webhookSecret]);
            Log::info('Received Signature:', ['signature' => $signature]);

            $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            Log::info('Expected Signature:', ['expectedSignature' => $expectedSignature]);

            if (!hash_equals($expectedSignature, $signature)) {
                Log::error('Invalid webhook signature');
                return $this->errorResponse('Invalid signature', 403);
            }

            $payload = $request->all();
            Log::info('Payload', $payload);
            $event = $payload['event'] ?? null;
            $payment = $payload['payload']['payment']['entity'] ?? null;

            $userId = $payment['notes']['user_id'] ?? null;
            $planId = $payment['notes']['plan_id'] ?? null;
            $orderId = $payment['notes']['order_id'] ?? null;
            $type = $payment['notes']['type'] ?? 1; // 1 for subscriptions, 2 for orders
            $amount = $payment['amount'] ?? null; // in paise
            $transactionId = $payment['id'] ?? null;
            $paymentStatus = $payment['status'] ?? 'failed';
            $failureReason = $payment['error_description'] ?? 'N/A';

            Log::info('Extracted Data:', [
                'userId' => $userId,
                'planId' => $planId,
                'orderId' => $orderId,
                'amount' => $amount,
                'transactionId' => $transactionId
            ]);

            if (!$userId || !$transactionId || !$amount) {
                Log::error('Missing required fields', [
                    'userId' => $userId,
                    'transactionId' => $transactionId,
                    'amount' => $amount
                ]);
                return $this->errorResponse('User or plan not found', 404);
            }

            $amountInRupees = $amount / 100;

            if ($type == 1) { // Subscription logic
                $plan = Plan::find($planId);
                $user = User::find($userId);

                if (!$plan || !$user) {
                    Log::error('User or Plan not found', [
                        'planId' => $planId,
                        'userId' => $userId
                    ]);
                    return $this->errorResponse('User or plan not found', 404);
                }

                if ($event === 'payment.authorized') {
                    $capture = $this->paymentService->capturePayment($transactionId, $amount);
                    $status = $capture ? 'success' : 'failed';
                } elseif ($event === 'payment.failed') {
                    $status = 'failed';
                } else {
                    return $this->errorResponse('Unhandled event type', 400);
                }

                $startDate = ($status === 'success') ? now() : null;
                $endDate = ($status === 'success') ? now()->addDays($plan->duration) : null;

                $subscriptionStatus = ($status === 'success') ? 'active' : 'pending';

                $existingSubscription = Subscription::updateOrCreate(
                    ['user_id' => $userId, 'type' => $plan->type],
                    [
                        'plan_id' => $planId,
                        'status' => $subscriptionStatus,
                        'start_date' => $startDate,
                        'end_date' => $endDate
                    ]
                );

                Log::info('Subscription processed', [
                    'status' => $subscriptionStatus,
                    'subscriptionId' => $existingSubscription->id
                ]);

                Transaction::create([
                    'type' => $plan->type,
                    'user_id' => $userId,
                    'transaction' => 2, // debit
                    'amount' => $amountInRupees,
                    'transaction_id' => $transactionId,
                    'subscription_id' => $existingSubscription->id,
                    'status' => $status,
                    'failure_reason' => $failureReason
                ]);

                Log::info('Transaction recorded');
            } else { // Order logic
                $order = Order::find($orderId);
                if (!$order) {
                    Log::error('Order not found', ['orderId' => $orderId]);
                    return $this->errorResponse('Order not found', 404);
                }
                if ($event === 'payment.authorized') {
                    $capture = $this->paymentService->capturePayment($transactionId, $amount);
                    $status = $capture ? 'success' : 'failed';
                } elseif ($event === 'payment.failed') {
                    $status = 'failed';
                } else {
                    return $this->errorResponse('Unhandled event type', 400);
                }

                $order->status = ($status === 'success') ? 'completed' : 'failed';
                $order->transaction_id = $transactionId;
                $order->save();

                if ($status === 'success') {
                    Cart::where('user_id', $userId)->where('subcategory_id', $order->subcategory_id)->forceDelete();

                    $serviceProviders = User::where('role', 2)->whereNotNull('device_token')->pluck('device_token')->toArray();
                    foreach ($serviceProviders as $token) {
                        Notification::create([
                            'user_id' => User::where('device_token', $token)->value('id'),
                            'title' => 'New Booking Received!',
                            'message' => "You have received a new booking (ID: {$order->booking_id}). Total Amount: ₹{$order->total_price}."
                        ]);
                    }

                    if (!empty($serviceProviders)) {
                        $title = 'New Booking Received!';
                        $message = "Booking ID: {$order->booking_id}, Amount: ₹{$order->total_price}.";
                        $this->notificationService->sendPushNotification($serviceProviders, $title, $message);
                    }
                } else {
                    Cart::where('user_id', $userId)->where('subcategory_id', $order->subcategory_id)->restore();
                }

                Transaction::create([
                    'type' => 3, // for orders
                    'user_id' => $userId,
                    'order_id' => $order->id,
                    'transaction' => 2, // debit
                    'amount' => $amountInRupees,
                    'transaction_id' => $transactionId,
                    'status' => $status,
                    'failure_reason' => $failureReason
                ]);
                Log::info('Order processed', [
                    'orderId' => $orderId,
                    'status' => $order->status
                ]);
            }

            return $this->successResponse('Webhook processed successfully', $status);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }

    public function testPayment()
    {
        try {
            $payment = $this->paymentService->capturePayment('pay_Q3S0bisKSeRSh1', 100);
            return $this->successResponse('Payment captured', $payment);
        } catch (\Exception $e) {
            return $this->errorResponse('Payment failed', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function privacy_policy()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $role = $this->user->role;

            if ($role == 1) {
                $type = 1;
            } elseif ($role == 2) {
                $type = 2;
            } else {
                return $this->errorResponse('Invalid role', 403);
            }

            $policy = PrivacyPolicy::where('type', $type)->select('type','content')->first();

            if (!$policy) {
                return $this->errorResponse('Privacy policy not found', 404);
            }

            return $this->successResponse($policy, 'Privacy policy retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function refund_policy()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $role = $this->user->role;

            if ($role == 1) {
                $type = 1;
            } elseif ($role == 2) {
                $type = 2;
            } else {
                return $this->errorResponse('Invalid role', 403);
            }

            $refundpolicy = RefundPolicy::where('type', $type)->select('type', 'content')->first();

            if (!$refundpolicy) {
                return $this->errorResponse('Refund policy not found', 404);
            }

            return $this->successResponse($refundpolicy, 'Refund policy retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function about_us()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $role = $this->user->role;

            if ($role == 1) {
                $type = 1;
            } elseif ($role == 2) {
                $type = 2;
            } else {
                return $this->errorResponse('Invalid role', 403);
            }

            $about = AboutUs::where('type', $type)->select('type', 'content')->first();

            if (!$about) {
                return $this->errorResponse('About Us content not found', 404);
            }

            return $this->successResponse($about, 'Privacy policy retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function contact_us()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $contact = ContactUs::select('phone_number', 'whatsapp_number', 'email', 'address')->first();

            if (!$contact) {
                return $this->errorResponse('Contact information not found', 404);
            }

            return $this->successResponse($contact, 'Contact information retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
    protected function social_profiles()
    {
        try {
            if (!$this->user) {
                return $this->errorResponse('User not found', 404);
            }

            $profile = SocialProfile::select('facebook', 'instagram', 'twitter', 'linkedin', 'youtube')->first();

            if (!$profile) {
                return $this->errorResponse('Social profiles not found', 404);
            }

            return $this->successResponse($profile, 'Social profiles retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Something went wrong', 500, ['error' => $e->getMessage()]);
        }
    }
}
