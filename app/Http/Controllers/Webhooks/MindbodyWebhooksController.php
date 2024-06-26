<?php

namespace App\Http\Controllers\Webhooks;

use App\Actions\CancelAppointmentFromMindbodyWebhookAction;
use App\Actions\CreateAppointmentFromMindbodyWebhookAction;
use App\Actions\CreateClientFromMindbodyWebhookAction;
use App\Actions\CreateLocationFromMindbodyWebhookAction;
use App\Actions\CreateStaffFromMindbodyWebhookAction;
use App\Actions\DeactivateClientFromMindbodyWebhookAction;
use App\Actions\DeactivateLocationFromMindbodyWebhookAction;
use App\Actions\DeactivateStaffFromMindbodyWebhookAction;
use App\Actions\MergeClientProfileFromMindbodyWebhookAction;
use App\Actions\UpdateAppointmentFromMindbodyWebhookAction;
use App\Actions\UpdateClientFromMindbodyWebhookAction;
use App\Actions\UpdateLocationFromMindbodyWebhookAction;
use App\Actions\UpdateStaffFromMindbodyWebhookAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MindbodyWebhooksController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $this->authenticateWebhookSender($request);

        $handlerMethodName = $this->getHandlerMethodName($request);

        if (method_exists($this, $handlerMethodName)) {
            try {
                $response = $this->{$handlerMethodName}($request);

                if ($response instanceof JsonResponse) {
                    return $response;
                }
            } catch (\Exception $e) {
                Log::critical('Something went wrong handling the webhook', ['error' => $e->getMessage()]);

                response('Webhook handled.', 200);
            }
        }

        $this->HandlerForMissingEvent();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function StaffCreatedWebhookHandler(Request $request)
    {
        $action = app(CreateStaffFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function StaffUpdatedWebhookHandler(Request $request)
    {
        $action = app(UpdateStaffFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function StaffDeactivatedWebhookHandler(Request $request)
    {
        $action = app(DeactivateStaffFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function LocationCreatedWebhookHandler(Request $request)
    {
        $action = app(CreateLocationFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function LocationUpdatedWebhookHandler(Request $request)
    {
        $action = app(UpdateLocationFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function LocationDeactivatedWebhookHandler(Request $request)
    {
        $action = app(DeactivateLocationFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function ClientCreatedWebhookHandler(Request $request)
    {
        $action = app(CreateClientFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function ClientUpdatedWebhookHandler(Request $request)
    {
        $action = app(UpdateClientFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function ClientDeactivatedWebhookHandler(Request $request)
    {
        $action = app(DeactivateClientFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function ClientProfileMergerCreatedWebhookHandler(Request $request)
    {
        $action = app(MergeClientProfileFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function AppointmentBookingCreatedWebhookHandler(Request $request)
    {
        $action = app(CreateAppointmentFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function AppointmentBookingUpdatedWebhookHandler(Request $request)
    {
        $action = app(UpdateAppointmentFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    protected function AppointmentBookingCancelledWebhookHandler(Request $request)
    {
        $action = app(CancelAppointmentFromMindbodyWebhookAction::class);

        $action->execute(data_get($request->all(), 'eventData'));

        return response()->json();
    }

    /**
     * @param $request
     * @return string
     */
    protected function getHandlerMethodName($request): string
    {
        return Str::studly(str_replace(".", "_", data_get($request->all(), 'eventId', 'no_method'))) . 'WebhookHandler';
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    protected function HandlerForMissingEvent()
    {
        return response('missing method called', 200);
    }

    protected function authenticateWebhookSender(Request $request): void
    {
        $signature = "sha256=" . base64_encode(
                hash_hmac(
                    'sha256',
                    $request->getContent(),
                    config('platform.mindbody.webhook_signature_key'),
                    true)
            );

        if ($signature !== $request->header('X-Mindbody-Signature')) {
            abort(404);
        }
    }
}
