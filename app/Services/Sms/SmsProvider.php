<?php

namespace App\Services\Sms;

use App\DTO\SmsSendResult;

/**
 * Contrat commun aux fournisseurs d'envoi de SMS.
 */
interface SmsProvider
{
    /**
     * Envoie un SMS à un destinataire.
     *
     * @param  string  $recipient  Numéro au format international (ex. +261340000000)
     * @param  string  $message  Contenu du SMS
     * @param  string|null  $idempotencyKey  Clé stable permettant au fournisseur de dédupliquer les tentatives
     */
    public function send(string $recipient, string $message, ?string $idempotencyKey = null): SmsSendResult;
}
