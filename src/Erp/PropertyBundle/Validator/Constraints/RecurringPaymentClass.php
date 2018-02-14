<?php

namespace Erp\PropertyBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RecurringPaymentClass extends Constraint
{
    public $dayUntilDueMessage = 'Rent due date {{ value }} is not accessible.';
    public $paymentAmountMessage = 'Payment amount {{ value }} is not accessible.';
    public $allowAutoDraftMessage = 'Recurring payment is not allowed.';
    public $allowRentPaymentMessage = 'You can not pay for rent due to indebtedness.';

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