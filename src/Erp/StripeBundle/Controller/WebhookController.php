<?php

namespace Erp\StripeBundle\Controller;

use Stripe\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WebhookController extends Controller
{
    public function notifyAction(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        $apiManger = $this->get('erp_stripe_entity_api_manager');
        $arguments = [
            'id' => $content['id'],
            'options' => null,
        ];
        $response = $apiManger->callStripeApi('\Stripe\Event', 'retrieve', $arguments);

        if (!$response->isSuccess()) {

        }
        /** @var Event $event */
        $event = $response->getContent();
        $guessedDispatchingEvent = $this->get('erp_stripe.event.event_guesser')->guess($event);

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch($guessedDispatchingEvent['type'], $guessedDispatchingEvent['object']);
    }
}
