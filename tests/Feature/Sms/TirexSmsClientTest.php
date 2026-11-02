<?php

namespace Tests\Feature\Sms;

use App\Exceptions\SmsProviderException;
use App\Services\Sms\SmsProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TirexSmsClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.sms.url', 'https://sms.test/api/v1/send-sms');
        config()->set('services.sms.key', 'test-key');
        config()->set('services.sms.sender', 'Job2You');
        config()->set('services.sms.timeout', 5);
    }

    public function test_it_sends_an_sms_and_parses_the_provider_response(): void
    {
        Http::fake([
            'sms.test/*' => Http::response([
                'success' => true,
                'data' => [
                    'id' => 'sms_123',
                    'status' => 'sent',
                ],
            ]),
        ]);

        $result = app(SmsProvider::class)->send('+261340000000', 'Bonjour', 'alert-1');

        $this->assertTrue($result->success);
        $this->assertSame('sms_123', $result->providerReference);
        $this->assertSame('sent', $result->providerStatus);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://sms.test/api/v1/send-sms'
                && $request->hasHeader('Authorization', 'Bearer test-key')
                && $request->hasHeader('Idempotency-Key', 'alert-1')
                && $request['to'] === '+261340000000'
                && $request['message'] === 'Bonjour'
                && $request['sender'] === 'Job2You';
        });
    }

    public function test_it_throws_the_provider_error_message(): void
    {
        Http::fake([
            'sms.test/*' => Http::response([
                'success' => false,
                'error' => [
                    'code' => 'invalid_phone',
                    'message' => 'Numéro de téléphone invalide.',
                ],
            ], 422),
        ]);

        $this->expectException(SmsProviderException::class);
        $this->expectExceptionMessage('Numéro de téléphone invalide.');

        app(SmsProvider::class)->send('+261000', 'Bonjour');
    }

    public function test_it_refuses_to_send_without_configuration(): void
    {
        config()->set('services.sms.key', '');

        Http::preventStrayRequests();

        $this->expectException(SmsProviderException::class);
        $this->expectExceptionMessage('configuration SMS est incomplète');

        app(SmsProvider::class)->send('+261340000000', 'Bonjour');
    }
}
