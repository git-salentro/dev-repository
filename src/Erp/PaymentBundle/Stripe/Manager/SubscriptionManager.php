<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Erp\PaymentBundle\Stripe\Client\Client;
use Stripe\Subscription;

class SubscriptionManager
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create($params)
    {
        return $this->client->sendSubscriptionRequest('create', $params);
    }

    public function retrieve($id)
    {
        return $this->client->sendSubscriptionRequest('retrieve', $id);
    }

    public function update(Subscription $subscription, $params)
    {
        return $this->client->sendUpdateRequest($subscription, $params);
    }
}
