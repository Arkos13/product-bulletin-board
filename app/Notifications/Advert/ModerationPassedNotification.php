<?php

namespace App\Notifications\Advert;

use App\Entity\Adverts\Advert\Advert;
use App\Notifications\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class ModerationPassedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $advert;

    /**
     * ModerationPassedNotification constructor.
     * @param Advert $advert
     */
    public function __construct(Advert $advert)
    {
        $this->advert = $advert;
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', SmsChannel::class];
    }

    /**
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Moderation is passed')
            ->greeting('Hello!')
            ->line('Your advert successfully passed a moderation.')
            ->action('View Advert', route('adverts.show', $this->advert))
            ->line('Thank you for using our application!');
    }

    /**
     * @return string
     */
    public function toSms(): string
    {
        return 'Your advert successfully passed a moderation.';
    }
}