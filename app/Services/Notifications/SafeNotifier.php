<?php

namespace App\Services\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Envoie une notification sans faire échouer l'action métier si le transport échoue.
 */
class SafeNotifier
{
    /**
     * Envoie la notification et journalise toute erreur de transport.
     */
    public function send(object $notifiable, Notification $notification): bool
    {
        try {
            $notifiable->notify($notification);

            return true;
        } catch (Throwable $exception) {
            Log::error('Échec de l’envoi d’une notification.', [
                'notification' => $notification::class,
                'notifiable_type' => $notifiable::class,
                'notifiable_id' => $notifiable->id ?? null,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
