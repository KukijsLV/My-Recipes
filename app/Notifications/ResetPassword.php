<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get('mail.subject_reset_password'))
            ->line(Lang::get('mail.reset_password_instruction'))
            ->action(Lang::get('mail.reset_password_button'), route('password.reset', $this->token));
    }
}
