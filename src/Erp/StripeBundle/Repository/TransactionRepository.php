<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;

class TransactionRepository extends EntityRepository
{
    public function getGroupedTransactions(StripeAccount $stripeAccount = null, StripeCustomer $stripeCustomer = null, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as gAmount, MONTH(t.created) as gMonth, YEAR(t.created) as gYear, CONCAT(YEAR(t.created), \'-\', MONTH(t.created)) as interval');

        if ($stripeAccount) {
            $qb->where('t.account = :account')
                ->setParameter('account', $stripeAccount);
            if ($stripeCustomer) {
                $qb->orWhere('t.customer = :customer')
                    ->setParameter('customer', $stripeCustomer);
            }
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

    public function getTransactions(StripeAccount $stripeAccount = null, StripeCustomer $stripeCustomer = null, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null)
    {
        $qb = $this->createQueryBuilder('t');

        if ($stripeAccount) {
            $qb->where('t.account = :account')
                ->setParameter('account', $stripeAccount);
            if ($stripeCustomer) {
                $qb->orWhere('t.customer = :customer')
                    ->setParameter('customer', $stripeCustomer);
            }
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
