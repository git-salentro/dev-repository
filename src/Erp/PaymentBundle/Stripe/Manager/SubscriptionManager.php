<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Stripe\Subscription;

class SubscriptionManager extends AbstractManager
{
    public function create($params, $options = null)
    {
        return $this->client->sendSubscriptionRequest('create', $params, $options);
    }

    public function retrieve($id, $options = null)
    {
        return $this->client->sendSubscriptionRequest('retrieve', $id, $options);
    }

    public function update(Subscription $subscription, $params, $options = null)
    {
        return $this->client->sendUpdateRequest($subscription, $params, $options);
    }
}
