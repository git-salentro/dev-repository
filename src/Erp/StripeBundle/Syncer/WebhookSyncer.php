<?php

namespace Erp\StripeBundle\Syncer;

use Stripe\Event;
use Stripe\ApiResource;

class WebhookSyncer extends AbstractSyncer
{
    public function syncLocalFromStripe(ApiResource $stripeResource)
    {
        if (!$stripeResource instanceof Event) {
            throw new \InvalidArgumentException('WebhookEventSyncer::syncLocalFromStripe() accepts only Stripe\Event objects as second parameter.');
        }

        $stripeObjectData = $stripeResource->data->object;
        switch ($stripeObjectData->object) {
            case 'charge':
                $this->getChargeSyncer()->syncLocalFromStripe($entity, $stripeObjectData);
                break;
            case 'invoice':
                $this->getChargeSyncer()->syncLocalFromStripe($entity, $stripeObjectData);
                break;
        }


    }
}