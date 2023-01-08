<?php

namespace App\Notifications;

use Bavix\Wallet\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ConfirmPaymentNotification extends Notification
{
    use Queueable;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * ConfirmPaymentNotification constructor.
     * @param string $token
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting( __('Hi :name, we have registered your purchase ":uuid"', ['name' => $notifiable->name, 'uuid' => $this->transaction->uuid]))
                    ->line(new HtmlString('Below we show you the confirmation code: <code>' . $this->transaction->token . '</code>'))
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
