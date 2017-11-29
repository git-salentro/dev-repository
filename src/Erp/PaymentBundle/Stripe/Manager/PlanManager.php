<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Erp\PaymentBundle\Stripe\Client\Client;
use Stripe\Plan;

class PlanManager
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
        return $this->client->sendPlanRequest('create', $params);
    }

    public function retrieve($id)
    {
        return $this->client->sendPlanRequest('retrieve', $id);
    }

    public function update(Plan $plan, $params)
    {
        return $this->client->sendUpdateRequest($plan, $params);
    }
}
