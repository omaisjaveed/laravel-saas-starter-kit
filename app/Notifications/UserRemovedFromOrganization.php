<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRemovedFromOrganization extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Organization $organization)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
            ->subject("You have been removed from {$this->organization->name}")
            ->greeting('Hello!')
            ->line("Your access to **{$this->organization->name}** has been removed.")
            ->line('If you believe this was a mistake, please contact the organization owner.')
            ->action('Go to dashboard', route('login'));
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
            'title' => "Removed from {$this->organization->name}",
            'message' => "Your access to {$this->organization->name} has been removed.",
            'organization_id' => $this->organization->id,
            'organization_name' => $this->organization->name,
            'type' => 'removal',
        ];
    }
}
