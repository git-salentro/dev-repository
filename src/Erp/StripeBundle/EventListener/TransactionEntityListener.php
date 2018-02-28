<?php

namespace Erp\StripeBundle\EventListener;

use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\LateRentPayment;
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

        $metadata = $entity->getMetadata();

        if (isset($metadata[LateRentPayment::RENT_PAYMENT_METADATA_KEY])) {
            $em = $this->registry->getManagerForClass(LateRentPayment::class);
            $repository = $em->getRepository(LateRentPayment::class);
            $rentPayment = $repository->find($metadata[LateRentPayment::RENT_PAYMENT_METADATA_KEY]);

            if ($rentPayment) {
                $rentPayment->addTransaction($entity);
                $em->persist($rentPayment);
                $em->flush();
            }

            return;
        }

        $em = $this->registry->getManagerForClass(RentPaymentBalance::class);

        $rentPaymentBalance = $user->getRentPaymentBalance();
        $rentPaymentBalance->depositMoneyToBalance($entity->getAmount());

        $em->persist($rentPaymentBalance);
        $em->flush();
    }
}
