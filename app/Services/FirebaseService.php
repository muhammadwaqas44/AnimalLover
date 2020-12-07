<?php

namespace App\Services;

use Illuminate\Http\Request;

class FirebaseService{

//    send notifications on postman
//    https://medium.com/android-school/test-fcm-notification-with-postman-f91ba08aacc3
//    https://medium.com/@sachinkhard/send-push-notification-via-firebase-by-postman-3f459ea5d170

    public static function sendPushNotification(Request $request){
        $client = new \GuzzleHttp\Client(['verify' => false ]);

        $request = $client->post(
            'https://fcm.googleapis.com/fcm/send',
            [
                'headers' => [
                    'Authorization' => 'key='.env('FIREBASE_AUTHORIZATION'),
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    "registration_ids" =>  $request->to,
                    "priority"=> "high",
                    "content_available" => true,
                    "mutable_content" => true,
                    "notification" => $request->dataNoti,
                    "data" => $request->dataBody
                ])
            ]
        );
        $response = $request->getBody();
        $response = json_decode($response);

        if($response->success > 0){
            return Helper::jsonResponse(1, 'Notification sent',$response,'');
        } else {
            return Helper::jsonResponse(1, 'Notification sent',$response,'');
        }
    }

    public static function send_notification($fcm_token,$text,$title)
    {
        $message = new RawMessageFromArray([
            'token' => !empty($fcm_token)?$fcm_token:'csOlRyWht0HQtNEHsQdIIE:APA91bFQAQz9dsE80tfdjRDYjKOWrhHeZu6Q_8E7F98ykBmyW6EPB8D4O7rKx-fxw7bJ6SaEGHd_EEF7x9ir1LiaOan4Tv6WwM-kR4opl1JgePqj_cOuTS6OXP-qb9BJy_B1PfTpG6e5',
            'notification' => [
// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#notification
                'title' => $title,
                'body' => $text,
                'image' => 'http://lorempixel.com/400/200/',
            ],
            'data' => [
                'key_1' => 'Value 1',
                'key_2' => 'Value 2',
            ],
            'android' => [
// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#androidconfig
                'ttl' => '3600s',
                'priority' => 'normal',
                'notification' => [
                    'title' => $title,
                    'body' => $text,
                    'icon' => '',
                    'color' => '',
                ],
            ],
            'apns' => [
// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'subtitle' => $text,
                            'body' => '',
                        ],
                        'badge' => 1,
                        'sound' => 'default',
                    ],
                ],
            ],
            'webpush' => [
// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#webpushconfig
                'notification' => [
                    'title' => '$GOOG up 1.43% on the day',
                    'body' => '$GOOG gained 11.80 points to close at 835.67, up 1.43% on the day.',
                    'icon' => 'https://my-server/icon.png',
                ],
            ],
            'fcm_options' => [
// https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#fcmoptions
                'analytics_label' => 'some-analytics-label'
            ]
        ]);
        $messaging = app('firebase.messaging');


        try{
            $messaging->send($message);
        }
        catch(\Exception $e) {
        }

    }
}
