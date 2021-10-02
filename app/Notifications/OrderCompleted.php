<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCompleted extends Notification
{
    use Queueable;

    private $order;
   
    /**
     * Create a new notification instance.
     *
     * @return void
     */

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
         return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
           'data' => $this->order->product->name . 'has been ordered by ' . $this->order->user->name
        ];
    }
}
