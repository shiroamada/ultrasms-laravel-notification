<?php

namespace NotificationChannels\UltraSms;

use Illuminate\Notifications\Notification;
use NotificationChannels\UltraSms\Exceptions\CouldNotSendNotification;

class UltraSmsChannel
{
    /** @var \NotificationChannels\UltraSms\UltraSmsApi */
    protected $ultrasms;

    public function __construct(UltraSmsApi $ultrasms)
    {
        $this->ultrasms = $ultrasms;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     *
     * @throws  \NotificationChannels\UltraSms\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('ultrasms');

        if (empty($to)) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $message = $notification->toUltraSms($notifiable);

        if (is_string($message)) {
            $message = new UltraSmsMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    protected function sendMessage($recipient, UltraSmsMessage $message)
    {
        $message->content = html_entity_decode($message->content, ENT_QUOTES, 'utf-8');
        $message->content = urlencode($message->content);

        //the sms format must start with 6
        $valid_mobile = '';

        if ($recipient[0] == '6') {
            $valid_mobile = '+'.$recipient;
        }

        if ($recipient[0] == '0') {
            $valid_mobile = '+6'.$recipient;
        }

        $params = [
            'to'        => $valid_mobile,
            'body'      => $message->content,
        ];

        if ($message->sendAt instanceof \DateTimeInterface) {
            $params['time'] = '0'.$message->sendAt->getTimestamp();
        }

        $this->ultrasms->send($params);
    }
}
