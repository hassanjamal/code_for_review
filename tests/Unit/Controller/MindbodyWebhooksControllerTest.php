<?php
use Tests\TestCase;

class MindbodyWebhooksControllerTest extends TestCase
{
    /** @test */
    public function it_aborts_a_webhook_if_source_is_not_known()
    {
        $data = [
            "messageId" => "ASwFMoA2Q5UKw69g3RDbvU",
            "eventId" => "staff.deactivated",
            "eventData" => [
            ],
        ];
        $signature = 'sha256='.base64_encode(hash_hmac('sha256', json_encode($data), 'some-unknown-secret', true));

        $response = $this->withHeaders([
            'X-Mindbody-Signature' =>  $signature,
        ])->postJson(route('mindbody.webhooks'), $data);

        $this->assertEquals($response->getStatusCode(), 404);
    }
}
