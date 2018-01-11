<?php

namespace Erp\PaymentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\StripeRecurringPayment;

class StripeRecurringPaymentRepository extends EntityRepository
{
    public function getScheduledRecurringPayments()
    {
        $qb = $this->getScheduledQueryBuilder();
        $qb->select('srp')
            ->andWhere('srp.type = :type')
            ->setParameter('type', StripeRecurringPayment::TYPE_RECURRING);

        return $qb->getQuery()->getResult();
    }

    public function getScheduledSinglePayments()
    {
        $qb = $qb = $this->getScheduledQueryBuilder();
        $qb->select('srp')
            ->andWhere('srp.type = :type')
            ->andWhere('srp.status = :status')
            ->setParameter('type', StripeRecurringPayment::TYPE_SINGLE)
            ->setParameter('status', StripeRecurringPayment::STATUS_PENDING);

        return $qb->getQuery()->getResult();
    }

    private function getScheduledQueryBuilder()
    {
        $date = (new \DateTime())->setTime(0, 0)->modify('-1 day');
        $qb = $this->createQueryBuilder('srp');

        return $qb->select('srp')
            ->where('srp.nextPaymentAt = :date')
            ->setParameter('date', $date);
    }
}
