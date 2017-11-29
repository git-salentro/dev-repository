<?php

namespace Erp\PaymentBundle\Stripe\Client;

use Stripe\Charge;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\Subscription;

class Client
{
    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var null|string
     */
    protected $apiVersion;

    /**
     * @param string $apiKey
     * @param string $apiVersion
     */
    public function __construct($apiKey, $apiVersion = null)
    {
        $this->apiKey = $apiKey;
        $this->apiVersion = $apiVersion;

        Stripe::setApiKey($this->apiKey);

        if ($this->apiVersion) {
            Stripe::setApiVersion($this->apiVersion);
        }
    }

    /**
     * @param $method
     * @param $params
     * @return Response
     */
    protected function sendChargeRequest($method, $params)
    {
        try {
            $response = Charge::$method($params);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    /**
     * @param $method
     * @param $params
     * @return Response
     */
    protected function sendTokenRequest($method, $params)
    {
        try {
            $response = Token::$method($params);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    /**
     * @param $method
     * @param $params
     * @return Response
     */
    public function sendCustomerRequest($method, $params)
    {
        try {
            $response = Customer::$method($params);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    /**
     * @param Customer $customer
     * @param $method
     * @param $params
     * @return Response
     */
    public function sendSourceRequest(Customer $customer, $method, $params)
    {
        try {
            $response = $customer->sources->{$method}($params);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    /**
     * @param $method
     * @param $params
     * @return Response
     */
    public function sendPlanRequest($method, $params)
    {
        try {
            $response = Plan::$method($params);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    /**
     * @param $method
     * @param $params
     * @return Response
     */
    public function sendSubscriptionRequest($method, $params)
    {
        try {
            $response = Subscription::$method($params);
        } catch (\Exception $e) {
            return new Response(null, $e);
        }

        return new Response($response);
    }

    public function sendUpdateRequest($object, $params)
    {
        foreach ($params as $prop => $value) {
            $object->{$prop} = $value;
        }

        $response = $object->save();

        return new Response($response);
    }
}
