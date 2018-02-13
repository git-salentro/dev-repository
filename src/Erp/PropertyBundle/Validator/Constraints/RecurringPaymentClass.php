<?php

namespace Erp\PropertyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RecurringPaymentClass extends Constraint
{
    public $dayUntilDueMessage = 'dayUntilDueMessage';
    public $paymentAmountMessage = 'paymentAmountMessage ';
    public $allowAutoDraftMessage = 'allowAutoDraftMessage';
    public $allowRentPaymentMessage = 'allowRentPaymentMessage';

    /**
     * @inheritdoc
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}