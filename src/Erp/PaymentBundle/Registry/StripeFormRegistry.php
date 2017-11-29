<?php

namespace Erp\PaymentBundle\Registry;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class StripeFormRegistry
{
    private $formTypes = [];

    public function __construct(array $formTypes = [])
    {
        $this->formTypes = $formTypes;
    }

    public function getForm($type)
    {
       if (!array_key_exists($type, $this->formTypes)) {
           throw new ParameterNotFoundException($type);
       }

       return $this->formTypes[$type];
    }
}