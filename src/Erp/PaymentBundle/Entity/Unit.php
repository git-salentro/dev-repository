<?php

namespace Erp\PaymentBundle\Entity;

class Unit
{
    /**
     * @var integer
     */
    private $count;

    /**
     * Set count
     *
     * @param $count
     *
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = (int) $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }
}