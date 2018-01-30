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
        if (!$account = $entity->getAccount()) {
            return;
        }

        //Expect Stripe Customer is Tenant
        $tenant = $entity->getCustomer()->getUser();

        if (!$tenant->getRentPayment()) {
            $rentPayment = new RentPayment();
            $rentPayment->setUser($tenant);
        } else {
            $rentPayment = $tenant->getRentPayment();
        }

        $rentPayment->depositMoneyToBalance($entity->getAmount());
        $rentPayment->setLastPaymentAt(new \DateTime());

        $em = $this->registry->getManagerForClass(RentPayment::class);
        $em->persist($rentPayment);
        $em->flush();
    }
}
