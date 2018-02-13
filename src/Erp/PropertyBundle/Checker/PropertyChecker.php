<?php

namespace Erp\PropertyBundle\Checker;

use Erp\PaymentBundle\Entity\StripeRecurringPayment;
use Erp\UserBundle\Entity\User;

class PropertyChecker
{
    public function isPayable(User $user, StripeRecurringPayment $recurringPayment)
    {
        $property = $user->getTenantProperty();

        if (!$user->hasRole(User::ROLE_TENANT)) {
            throw new \RuntimeException('Only tenant can pay for rent.');
        }

        $propertySettings = $property->getSettings();

        $userDayDue = $recurringPayment->getStartPaymentAt()->format('n');
        $dayUntilDue = $propertySettings->getDayUntilDue();
        if ($dayUntilDue != $userDayDue) {
            return false;
        }

        if ($recurringPayment->getAmount() != $propertySettings->getPaymentAmount()) {
            return false;
        }

        $isRecurringPayment = $recurringPayment->getType() === $recurringPayment::TYPE_RECURRING;
        if ($propertySettings->getAllowAutoDraft() !== $isRecurringPayment) {
            return false;
        }

        if ($lateRentPaymentSettings = $user->getLateRentPaymentSettings()) {
            if (!$lateRentPaymentSettings->isAllowRentPayment()) {
                return false;
            }
        }

        return true;
    }
}