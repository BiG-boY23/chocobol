<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOnlineRegistration extends Notification
{
    use Queueable;

    protected $registration;

    /**
     * Create a new notification instance.
     */
    public function __construct($registration)
    {
        $this->registration = $registration;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Online Vehicle Registration')
            ->line('A new vehicle registration has been submitted online.')
            ->line('Applicant: ' . $this->registration->full_name)
            ->line('Role: ' . ucfirst($this->registration->role))
            ->line('Plate Number: ' . $this->registration->plate_number)
            ->action('View Registration', route('office.users')) // Or a specific link
            ->line('Please review the documents and verify the application.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'registration_id' => $this->registration->id,
            'full_name' => $this->registration->full_name,
            'role' => $this->registration->role,
            'plate_number' => $this->registration->plate_number,
            'message' => 'New online registration from ' . $this->registration->full_name,
            'type' => 'online_registration'
        ];
    }
}
