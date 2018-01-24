<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\StripeAccount;

class TransactionRepository extends EntityRepository
{
    public function getGroupedTransactions(StripeAccount $stripeAccount = null, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as gAmount, MONTH(t.created) as gMonth, YEAR(t.created) as gYear, CONCAT(YEAR(t.created), \'-\', MONTH(t.created)) as interval');

        if ($stripeAccount) {
            $qb->where('t.owner = :owner')
                 ->setParameter('owner', $stripeAccount);
        }

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('t.created', ':dateFrom', ':dateTo'))
                    ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('t.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        $qb->groupBy('gYear')
            ->addGroupBy('gMonth');

        return $qb->getQuery()->getResult();
    }

    public function getTransactions(StripeAccount $stripeAccount = null, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('t');

        if ($stripeAccount) {
            $qb->where('t.owner = :owner')
                ->setParameter('owner', $stripeAccount);
        }

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('t.created', ':dateFrom', ':dateTo'))
                    ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('t.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        if ($type) {
            $qb->andWhere(
                $qb->expr()->in(
                    't.type',
                    $type
                )
            );
        }

        return $qb->getQuery();
    }
}
