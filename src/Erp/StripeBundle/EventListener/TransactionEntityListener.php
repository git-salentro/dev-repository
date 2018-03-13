<?php

namespace Erp\StripeBundle\EventListener;

use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\RentPaymentBalance;
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

        $em = $this->registry->getManagerForClass(RentPaymentBalance::class);

        $rentPaymentBalance = $user->getRentPaymentBalance();
        $rentPaymentBalance->depositMoneyToBalance($entity->getAmount());

        $em->persist($rentPaymentBalance);
        $em->flush();
    }
}
