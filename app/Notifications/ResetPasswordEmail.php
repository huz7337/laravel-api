<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ResetPasswordEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public string $token;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mailgun'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toMailgun($notifiable)
    {
        return [
            'subject' => __('Reset your password on Train Like Legends'),
            'template' => 'reset-password-request',
            'placeholders' => [
                'first_name' => $notifiable->first_name,
                'password_reset_link' => $this->getActionUrl($notifiable)
            ]
        ];

    }


    /**
     * @param $notifiable
     * @return string
     */
    protected function getActionUrl($notifiable): string
    {
        $url = env('PROVIDER_PORTAL_URL');
        return rtrim($url, '/') . '/authentication/reset-password?token=' . $this->token;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
