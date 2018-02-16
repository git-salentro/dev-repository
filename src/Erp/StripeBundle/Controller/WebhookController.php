<?php

namespace Erp\StripeBundle\Controller;

use Stripe\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    //TODO Add logger
    public function notifyAction(Request $request)
    {
        //TODO file_get_contents('php://input') empty with http
        $content = json_decode($request->getContent(), true);
        $apiManger = $this->get('erp_stripe.entity.api_manager');
        $arguments = [
            'id' => $content['id'],
            'options' => null,
        ];
        $response = $apiManger->callStripeApi('\Stripe\Event', 'retrieve', $arguments);

        if (!$response->isSuccess()) {
            return new Response('ok');
        }
        /** @var Event $event */
        $event = $response->getContent();

        if (!$event instanceof Event) {
            return new Response('ok');
        }

        $guessedDispatchingEvent = $this->get('erp_stripe.event.event_guesser')->guess($event);

        if (!$guessedDispatchingEvent) {
            return new Response('ok');
        }

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch($guessedDispatchingEvent['type'], $guessedDispatchingEvent['object']);

        return new Response('ok');
    }
}
