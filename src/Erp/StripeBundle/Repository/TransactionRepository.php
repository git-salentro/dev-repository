<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\StripeAccount;

class TransactionRepository extends EntityRepository
{
    public function getGroupedTransactions(StripeAccount $stripeAccount = null, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as gAmount, MONTH(t.created) as gMonth');

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

        $qb->groupBy('gMonth');

        return $qb->getQuery()->getResult();
    }
}
