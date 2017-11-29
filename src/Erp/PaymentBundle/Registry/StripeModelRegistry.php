<?php

namespace Erp\PaymentBundle\Registry;

use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class StripeModelRegistry
{
    private $models = [];

    public function __construct(array $models = [])
    {
        $this->models = $models;
    }

    public function getModel($type)
    {
        if (!array_key_exists($type, $this->models)) {
            throw new ParameterNotFoundException($type);
        }

        return new $this->models[$type]();
    }
}