<?php


namespace App\Channels;


use Illuminate\Notifications\Notification;
use Mailgun\Mailgun;
use Mailgun\Model\Message\SendResponse;
use Psr\Http\Message\ResponseInterface;

class MailgunChannel
{

    /**
     * @param $notifiable
     * @param Notification $notification
     * @return SendResponse|ResponseInterface
     */
    public function send($notifiable, Notification $notification)
    {
        $client = Mailgun::create(env('MAILGUN_API_KEY'));
        $domain = env('MAILGUN_DOMAIN');
        $fromName = env('MAIL_FROM_NAME');
        $fromAddress = env('MAIL_FROM_ADDRESS');

        $data = $notification->toMailgun($notifiable);

        $params = [
            'from' => "$fromName <$fromAddress>",
            'to' => "$notifiable->first_name $notifiable->last_name <$notifiable->email>",
            'subject' => $data['subject'],
            'template' => $data['template']
        ];

        if (isset($data['placeholders'])) {
            foreach ($data['placeholders'] as $key => $value) {
                $params["v:$key"] = $value;
            }
        }

        return $client->messages()->send($domain, $params);
    }

}
