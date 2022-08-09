<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ClientAcceptedInvitationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var User $_athlete
     */
    private User $_athlete;


    /**
     * ClientAcceptedInvitationEmail constructor.
     * @param User $athlete
     */
    public function __construct(User $athlete)
    {
        $this->_athlete = $athlete;
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
            'subject' => $this->_athlete->first_name . ' ' . $this->_athlete->last_name . ' ' . __('has created their account'),
            'template' => 'athlete-accepted-invitation',
            'placeholders' => [
                'first_name' => $notifiable->first_name,
                'athlete_name' => $this->_athlete->first_name . ' ' . $this->_athlete->last_name,
                'user_role' => User::ROLE_ATHLETE,
                'athlete_profile_link' => $this->getActionUrl($this->_athlete)
            ]
        ];
    }


    /**
     * @param User $athlete
     * @return string
     */
    protected function getActionUrl(User $athlete): string
    {
        $url = env('PROVIDER_PORTAL_URL');
        return rtrim($url, '/') . '/athletes/' . $athlete->id;
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
