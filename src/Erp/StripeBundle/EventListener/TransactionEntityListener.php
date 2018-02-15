<?php

namespace Erp\StripeBundle\EventListener;

use Erp\StripeBundle\Entity\Transaction;
use Erp\PropertyBundle\Entity\RentPayment;
use Erp\UserBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;

class TransactionEntityListener
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function postPersist(Transaction $entity)
    {
        $user = $entity->getCustomer()->getUser();
        if (!$user->hasRole(User::ROLE_TENANT)) {
            return;
        }

        if (!$user->getRentPayment()) {
            $rentPayment = new RentPayment();
            $rentPayment->setUser($user);
        } else {
            $rentPayment = $user->getRentPayment();
        }

        $rentPayment->depositMoneyToBalance($entity->getAmount());

        $em = $this->registry->getManagerForClass(RentPayment::class);
        $em->persist($rentPayment);
        $em->flush();
    }
}
