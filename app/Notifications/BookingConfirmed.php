<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingConfirmed extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
    return (new MailMessage)
        ->subject('Booking Confirmed')
        ->line("Your booking for event '{$this->booking->ticket->event->title}' is confirmed!")
        ->line('Thank you for booking with us.');
    }
}
