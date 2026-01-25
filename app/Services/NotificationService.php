<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    protected $firebaseUrl = 'https://fcm.googleapis.com/v1/projects/apka-budget-partner/messages:send';
    protected $accessToken;

    public function __construct()
    {
        $serviceAccount = json_decode(file_get_contents(storage_path('app/apka-budget-partner-firebase-adminsdk-fbsvc-509ccf49ab.json')), true);
        $this->accessToken = $this->getAccessToken($serviceAccount);
    }

     /**
     * Send Push Notification via Firebase
     *
     * @param array $deviceTokens
     * @param array $notification = Array(
            title=>
            body => 
            image =>
        ); 
     * @param string $body
     * @param string|null $sound
     * @param string|null $channelId
     * @return array
     */
    public function send(array $deviceTokens, array $notification, array $data = [], ?string $sound = 'incoming_tone'): array|bool{
        if (empty($deviceTokens)) {
            Log::error('Device tokens are empty.');
            return false;
        }
    
        $title = $notification['title'] ?? '';
        $body  = $notification['body'] ?? '';
    
        $response = [];
    
        foreach ($deviceTokens as $token) {
            $notificationData = [
                'message' => [
                    'token'        => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => !empty($data) ? array_map(fn($v) => (string)$v, $data) : new \stdClass(),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'title'      => $title,
                            'body'       => $body,
                            'sound'      => $sound, // should match res/raw/<sound>.mp3
                            'channel_id' => 'default_v2',
                        ],
                    ],
                    // Uncomment for iOS push notifications
                    // 'apns' => [
                    //     'payload' => [
                    //         'aps' => [
                    //             'sound' => $sound
                    //         ]
                    //     ]
                    // ]
                ]
            ];
            $response['notificationData'][] = $notificationData;
            $result = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type'  => 'application/json'
            ])->post($this->firebaseUrl, $notificationData);
    
            $response[] = [
                'token'        => $token,
                'firebaseUrl'  => $this->firebaseUrl,
                'status'       => $result->successful() ? 'success' : 'failed',
                'response'     => $result->body()
            ];
    
            if ($result->failed()) {
                Log::error('Failed to send notification to ' . $token . ': ' . $result->body());
            }
        }
        return $response;
    }


    public function test($deviceTokens, $title, $body, $sound = 'default'){
        $response = Array();
        $serviceAccount = json_decode(file_get_contents(storage_path('app/apka-budget-partner-firebase-adminsdk-fbsvc-509ccf49ab.json')), true);
        $response['serviceAccount'] = $serviceAccount;
        $response['accessToken'] =  $this->getAccessToken($serviceAccount);
        
        if (empty($deviceTokens)) {
            Log::error('Device tokens are empty.');
            return false;
        }

        $tokens = is_array($deviceTokens) ? $deviceTokens : [$deviceTokens];

        foreach ($tokens as $token) {
            // $notificationData = [
            //     'message' => [
            //         'token' => $token,
            //         'notification' => [
            //             'title' => $title,
            //             'body' => $body,
            //         ],
            //         'data' => [
            //             'title' => $title,
            //             'body' => $body,
            //             'FRAGMENT_TO_OPEN' => 'BookingFragment'
            //         ],
            //         'android' => [
            //             'priority' => 'high',
            //             // 'notification' => [
            //                 // 'sound' => $sound
            //                 // 'sound' => 'incoming_tone.mp3',
            //                 // 'channel_id' => 'default'
            //             // ]
            //         ],
            //         'apns' => [
            //             'payload' => [
            //                 'aps' => [
            //                     // 'sound' => $sound
            //                     'sound' => 'incoming_tone',
            //                     'channel_id' => 'default'
            //                 ]
            //             ]
            //         ]
            //     ]
            // ];
            
            // $notificationData = [
            //     'message' => [
            //         'token' => $token,
            //         // ANDROID configuration
            //         'android' => [
            //             'priority' => 'HIGH',
            //             'notification' => [
            //                 'title' => $title,
            //                 'body' => $body,
            //             //     // 'sound' => 'incoming_tone', // no .mp3, must match res/raw/incoming_tone.mp3
            //             //     // 'channel_id' => 'default'
            //             ]
            //         ],
            //         // 'apns' => [
            //         //     'payload' => [
            //         //         'aps' => [
            //         //             'alert' => [
            //         //                 'title' => $title,
            //         //                 'body' => $body
            //         //             ],
            //         //             'sound' => 'incoming-tone.caf' // iOS needs extension
            //         //         ]
            //         //     ]
            //         // ],
               
            //         'data' => [
            //             'title' => $title,
            //             'body' => $body,
            //             'FRAGMENT_TO_OPEN' => 'BookingFragment'
            //         ]
            //     ]
            // ];
            $notificationData = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'image' => '',
                        
                    ],
                    // Data Which want to send to app
                    'data' => [
                        'title' => $title,
                        'body' => $body,
                        // 'FRAGMENT_TO_OPEN' => 'BookingFragment'
                    ],
                    'android' => [
                        'priority'  => 'high',
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                            // 'default_sound' => true,
                            'sound' => 'incoming_tone', // no .mp3, must match res/raw/incoming_tone.mp3
                            'channel_id' => 'default_v2',
                            // 'color' => '#f01111'
                        ],
            
                    ],
                    // 'apns' => [
                    //     'payload' => [
                    //         'aps' => [
                    //             'sound' => $sound
                    //         ]
                    //     ]
                    // ]
                ]
            ];


          

            $result = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->firebaseUrl, $notificationData);
            
            $response['firebaseUrl'] = $this->firebaseUrl;
            $response['result'] = $result;
            
            if ($result->failed()) {
                $response['result_body'] = $result->body();
                $response['result_body_64'] = base64_decode($result->body(), true);
                Log::error('Failed to send notification: ' . $result->body());
            }
        }

        return $response;
        
    }
    public function sendPushNotification($deviceTokens, $title, $body, $sound = 'default')
    {
        if (empty($deviceTokens)) {
            Log::error('Device tokens are empty.');
            return false;
        }

        $tokens = is_array($deviceTokens) ? $deviceTokens : [$deviceTokens];

        foreach ($tokens as $token) {
            $notificationData = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'data' => [
                        'title' => $title,
                        'body' => $body,
                        'FRAGMENT_TO_OPEN' => 'BookingFragment'
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => $sound, // for new app
                            'channel_id' => 'default_v2', // for new app
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => $sound
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->firebaseUrl, $notificationData);

            if ($response->failed()) {
                Log::error('Failed to send notification: ' . $response->body());
            }
        }

        return true;
    }

    protected function getAccessToken($serviceAccount)
    {
        $jwt = $this->createJwt($serviceAccount);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        return $response->json()['access_token'] ?? null;
    }

    protected function createJwt($serviceAccount)
    {
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => time() + 3600,
            'iat' => time()
        ]));

        $signature = '';
        openssl_sign("$header.$payload", $signature, openssl_pkey_get_private($serviceAccount['private_key']), 'sha256');
        $signature = base64_encode($signature);

        return "$header.$payload.$signature";
    }
}
