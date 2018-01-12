<?php

namespace Erp\PaymentBundle\Stripe\Manager;


class EventManager extends AbstractManager
{
    public function retrieve($id, $options = null)
    {
        return $this->client->sendEventRequest('retrieve', $id, $options);
    }

}
