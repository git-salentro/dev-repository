<?php

namespace Erp\StripeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Event;

//TODO Create StripeBundle instead of heap of payments system
class WebhookController extends Controller
{
    public function notifyAction(Request $request)
    {
        /** @var Event $content */
        $content = json_decode($request->getContent(), true);

        $webhookSyncer = $this->get('erp.stripe.syncer.webhook_syncer');

        $webhookSyncer->syncLocalFromStripe($content);
    }
}