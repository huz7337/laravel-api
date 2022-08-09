<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ClientInvitationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The user that triggered the invitation
     * @var User
     */
    public User $_currentUser;

    public function __construct(User $currentUser)
    {
        $this->_currentUser = $currentUser;
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
    public function toMailgun(User $notifiable)
    {
        return [
            'subject' => __('You\'ve been invited to Train Like Legends'),
            'template' => 'athlete-invitation',
            'placeholders' => [
                'first_name' => $notifiable->first_name,
                'trainer_name' => $notifiable->trainer ?
                    $notifiable->trainer->first_name . ' ' . $notifiable->trainer->last_name :
                    $this->_currentUser->first_name . ' ' . $this->_currentUser->last_name,
                'invitation_code' => $notifiable->invitation->code
            ]
        ];
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
