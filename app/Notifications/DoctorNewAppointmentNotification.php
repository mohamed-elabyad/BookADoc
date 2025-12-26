<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DoctorNewAppointmentNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Appointment $appointment) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Appointment')
            ->line("Hello Dr. {$this->appointment->doctor->name}")
            ->line("you have a new appointment")
            ->line("Patient: {$this->appointment->user->name}")
            ->line("Date: {$this->appointment->date->format('j/n/Y')}")
            ->line("Time: {$this->appointment->time->format('g:i A')}")
            ->line('Make sure to confirm the appointment if it is not')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
