<?php

namespace Erp\PropertyBundle\Validator\Constraints;

use Erp\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RecurringPaymentClassValidator extends ConstraintValidator
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof RecurringPaymentClass) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\Length');
        }

        if (null === $value) {
            return;
        }

        /** @var User $user */
        if (!$user = $this->tokenStorage->getToken()->getUser()) {
            throw new \RuntimeException('User must be specified.');
        }

        if (!$user->hasRole(User::ROLE_TENANT)) {
            throw new \RuntimeException('Only tenant can pay for rent.');
        }

        if (!$property = $user->getTenantProperty()) {
            throw new \RuntimeException('User must have the property.');
        }

        if (!$propertySettings = $property->getSettings()) {
            throw new \RuntimeException('Property must have the payment settings.');
        }

        $userDayDue = $value->getStartPaymentAt()->format('n');
        $dayUntilDue = $propertySettings->getDayUntilDue();
        if ($dayUntilDue != $userDayDue) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->dayUntilDueMessage)
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->dayUntilDueMessage)
                    ->addViolation();
            }
        }

        if ($value->getAmount() != $propertySettings->getPaymentAmount()) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->paymentAmountMessage)
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->paymentAmountMessage)
                    ->addViolation();
            }
        }

        if ($value->isRecurring()) {
            if (!$propertySettings->getAllowAutoDraft()) {
                if ($this->context instanceof ExecutionContextInterface) {
                    $this->context->buildViolation($constraint->allowAutoDraftMessage)
                        ->addViolation();
                } else {
                    $this->buildViolation($constraint->allowAutoDraftMessage)
                        ->addViolation();
                }
            }
        }

        $rentPayment = $user->getRentPayment();
        //TODO Refactoring fee, rent tenant payment
        if ($rentPayment->getBalance() < 0) {
            if ($lateRentPaymentSettings = $user->getLateRentPaymentSettings()) {
                if ($value->getCategory() == 'rent' && !$lateRentPaymentSettings->isAllowRentPayment()) {
                    if ($this->context instanceof ExecutionContextInterface) {
                        $this->context->buildViolation($constraint->allowRentPaymentMessage)
                            ->addViolation();
                    } else {
                        $this->buildViolation($constraint->allowRentPaymentMessage)
                            ->addViolation();
                    }
                }
            } else {
                if ($this->context instanceof ExecutionContextInterface) {
                    $this->context->buildViolation($constraint->allowRentPaymentMessage)
                        ->addViolation();
                } else {
                    $this->buildViolation($constraint->allowRentPaymentMessage)
                        ->addViolation();
                }
            }
        }
    }
}