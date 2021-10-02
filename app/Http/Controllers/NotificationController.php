<?php

namespace App\Http\Controllers;

use App\Helper\Message;
use Illuminate\Http\Request;
use App\Helper\AuthorizeUser;

class NotificationController extends Controller
{   
    use Message, AuthorizeUser;
    
    public function getNotification()
    {   

        if( ! $this->authorizeUser('isAdmin') ) {
            return $this->error('You are not authorize to see notification!!', null, 403);
        }
 
        $notifications = \DB::table('notifications')
                    ->get();
        $data = [];
        
        $count = \DB::table('notifications')
                    ->where('read_at',NULL)
                    ->count();
        
        if($count > 0){
            foreach ($notifications as $notification) {
                $data[] = json_decode($notification->data);
            }           
        }
        
        return $this->success('All Unread Notifications!', $data, 'notifications', 200);
    }

    public function getOrderActivity()
    {   
      
        if($this->authorizeUser('isAdmin') ) {

            $collection = \DB::table('audits')
                    ->get();

            return [
                'data' => $collection->map(function($data) {
                    return [
                        'id'         => $data->id,
                        'user'       => \App\Models\User::find($data->user_id)->name . ' whose user id is '.$data->user_id,
                        'event'      => $data->event,
                        'old_values' => json_decode($data->old_values),
                        'new_values' => json_decode($data->new_values),
                        'url'        => $data->url,
                        'ip_address' => $data->ip_address,
                        'user_agent' => $data->user_agent,
                        'created_at' => \Carbon\Carbon::parse($data->created_at)->toDayDateTimeString(),
                        'updated_at' => \Carbon\Carbon::parse($data->updated_at)->toDayDateTimeString(),
                    ];
                })
            ];
            
        }

        $collection = \DB::table('audits')
                ->where('user_id',auth()->id())
                ->get();

        return [
            'data' => $collection->map(function($data) {
                return [
                    'id'         => $data->id,
                    'user'       => \App\Models\User::find($data->user_id)->name . ' whose user id is '.$data->user_id,
                    'event'      => $data->event,
                    'old_values' => json_decode($data->old_values),
                    'new_values' => json_decode($data->new_values),
                    'url'        => $data->url,
                    'ip_address' => $data->ip_address,
                    'user_agent' => $data->user_agent,
                    'created_at' => \Carbon\Carbon::parse($data->created_at)->toDayDateTimeString(),
                    'updated_at' => \Carbon\Carbon::parse($data->updated_at)->toDayDateTimeString(),
                ];
            })
        ];

    }
}
