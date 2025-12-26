<?php

namespace App\Http\Controllers\Stripe;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends Controller
{
    public function __construct(protected StripeService $stripeService) {}

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $webhook_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $webhook_secret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Webhook invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Webhook invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->stripeService->confirmPayment($event);
                break;

            case 'checkout.session.expired':
                $this->stripeService->expiredPayment($event);
                break;

            case 'payment_intent.payment_failed':
                $this->stripeService->failedPayment($event);
                break;

            default:
                Log::info('Unhandled webhook event', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }
}
