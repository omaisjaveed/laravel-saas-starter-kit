<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleChangedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Organization $organization, public string $role)
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
            ->subject("Your role in {$this->organization->name} has changed")
            ->greeting('Hello!')
            ->line("Your role in **{$this->organization->name}** has been changed to **{$this->role}**.")
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
            'title' => "Role changed in {$this->organization->name}",
            'message' => "Your role in {$this->organization->name} is now {$this->role}.",
            'organization_id' => $this->organization->id,
            'organization_name' => $this->organization->name,
            'role' => $this->role,
            'type' => 'role_changed',
        ];
    }
}
