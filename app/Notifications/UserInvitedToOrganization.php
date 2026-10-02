<?php

namespace App\Notifications;

use App\Models\Organization;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitedToOrganization extends Notification
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
            ->subject("You have been invited to {$this->organization->name}")
            ->greeting('Hello!')
            ->line("You have been added to **{$this->organization->name}** as **{$this->role}**.")
            ->line('If you do not have a password yet, use the "Forgot password" option on the login page to set one using your email address.')
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
            'title' => "Invited to {$this->organization->name}",
            'message' => "You have been added to {$this->organization->name} as {$this->role}.",
            'organization_id' => $this->organization->id,
            'organization_name' => $this->organization->name,
            'role' => $this->role,
            'type' => 'invitation',
        ];
    }
}
