<?php

namespace Tests\Feature\Webhooks\Mindbody;

trait WebhookHttpRequest
{
    private function receiveFakeWebhook($overrides)
    {
        list($data, $signature) = $this->getSignature($overrides);

        return $this->withHeaders([
            'X-Mindbody-Signature' =>  $signature,
        ])->postJson(route('mindbody.webhooks'), $data);
    }

    private function getSignature($overrides): array
    {
        $data = $this->getFakeData($overrides);

        $signature = 'sha256=' . base64_encode(hash_hmac('sha256', json_encode($data), config('platform.mindbody.webhook_signature_key'), true));

        return array($data, $signature);
    }
}
