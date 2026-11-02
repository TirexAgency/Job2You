<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notification personnalisée de vérification d'email avec template Job2You.
 * Ajoute le logo, un bouton "Cliquez ici" et un design moderne.
 */
class VerifyEmailCustom extends VerifyEmail
{
    use Queueable;

    /**
     * Crée le message mail pour la notification.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Vérifiez votre adresse email — Job2You')
            ->view('emails.verify-email', [
                'user' => $notifiable,
                'verificationUrl' => $verificationUrl,
                'appName' => config('app.name'),
            ]);
    }
}