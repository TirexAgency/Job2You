<?php

namespace App\Console\Commands;

use App\Exceptions\SmsProviderException;
use App\Jobs\SendSmsJob;
use App\Models\User;
use App\Services\Sms\SmsProvider;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('sms:send {to : Numéro destinataire au format international} {message : Message à envoyer} {--user= : ID du candidat dont le quota doit être consommé}')]
#[Description('Envoie un SMS de test ou met en file un SMS pour un candidat')]
class SendSmsCommand extends Command
{
    /**
     * Exécute l'envoi ou la mise en file du SMS.
     */
    public function handle(SmsProvider $provider): int
    {
        $recipient = (string) $this->argument('to');
        $message = (string) $this->argument('message');
        $userId = $this->option('user');

        if ($userId !== null) {
            $user = User::find($userId);

            if (! $user) {
                $this->error('Utilisateur introuvable.');

                return self::FAILURE;
            }

            SendSmsJob::dispatch(
                $user->id,
                null,
                $recipient,
                $message,
                'manual:'.$user->id.':'.Str::uuid(),
            );

            $this->info('SMS mis en file pour l’utilisateur #'.$user->id.'.');

            return self::SUCCESS;
        }

        try {
            $result = $provider->send($recipient, $message, 'manual:'.Str::uuid());
        } catch (SmsProviderException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('SMS envoyé. Référence fournisseur : '.($result->providerReference ?? '—'));

        return self::SUCCESS;
    }
}
