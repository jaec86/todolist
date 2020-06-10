<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Notification
{
    private $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = 'http://' . env('FRONTEND_USER_DOMAIN', 'localhost:8080') . '/password/reset/' . $this->token . '?email=' . $notifiable->getEmailForPasswordReset();

        return (new MailMessage)
                    ->subject('Reset Password Notification')
                    ->line('You are receiving this email because we received a password reset request for your account.')
                    ->action('Reset Password', $url)
                    ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
                    ->line('If you did not request a password reset, no further action is required.');
    }
}
