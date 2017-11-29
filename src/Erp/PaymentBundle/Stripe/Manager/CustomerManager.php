<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Erp\PaymentBundle\Stripe\Client\Client;
use Stripe\Customer;

class CustomerManager
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
        return $this->client->sendCustomerRequest('create', $params);
    }

    public function retrieve($id)
    {
        return $this->client->sendCustomerRequest('retrieve', $id);
    }

    public function createCard(Customer $customer, $params)
    {
        $params = array_merge($params, ['object' => 'card']);

        return $this->client->sendSourceRequest($customer, 'create', ['source' => $params]);
    }

    public function createBankAccount(Customer $customer, $params)
    {
        $params = array_merge($params, ['object' => 'bank_account']);

        return $this->client->sendSourceRequest($customer, 'create', ['source' => $params]);
    }

    public function retrieveBankAccount(Customer $customer, $id)
    {
        return $this->client->sendSourceRequest($customer, 'retrieve', $id);
    }

    public function retrieveCreditCard(Customer $customer, $id)
    {
        return $this->client->sendSourceRequest($customer, 'retrieve', $id);
    }
}