<?php

namespace Erp\StripeBundle\Controller;

use Stripe\Event;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\StripeBundle\Event\InvoiceEvent;
use Erp\StripeBundle\Event\ChargeEvent;

class WebhookController extends Controller
{
    public function notifyAction(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $eventManger = $this->get('erp.payment.stripe.manager.event_manager');
        /** @var Event $event */
        $event = $eventManger->retrieve($content['id']);

        $guessedDispatchingEvent = $this->get('erp_stripe.event.event_guesser')->guess($event);

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch($guessedDispatchingEvent['type'], $guessedDispatchingEvent['object']);
    }
}
