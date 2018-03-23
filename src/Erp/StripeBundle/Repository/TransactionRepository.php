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
        }

        if ($stripeCustomer) {
            $qb->orWhere('t.customer = :customer')
                ->setParameter('customer', $stripeCustomer);
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

    public function getTransactionsQuery(StripeAccount $stripeAccount = null, StripeCustomer $stripeCustomer = null, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.created', 'DESC');

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


    public function getTransactionsBothDirectionsQuery($stripeAccounts = null, $stripeCustomers = null, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null, $keyword = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.created', 'DESC');
        $qb->leftJoin('ErpPaymentBundle:StripeAccount','sa', 'WITH', 'sa.id = t.account');
        $qb->leftJoin('ErpPaymentBundle:StripeCustomer','sc', 'WITH', 'sc.id = t.customer');

        if ($stripeAccounts) {  //outgoing transaction (account -> customer)
            $qb
                ->andWhere(
                    $qb->expr()->in(
                        't.account',
                        ':accounts'
                    )
                )
                ->setParameter('accounts', $stripeAccounts);
            if ($stripeCustomers) {
                $qb
                    ->orWhere(
                        $qb->expr()->in(
                            't.customer',
                            ':customers'
                        )
                    )
                    ->setParameter('customers', $stripeCustomers);
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

        if ($keyword) {
            //TODO: search by several fields (create index)
            $qb->andWhere(
                $qb->expr()->like('sa.lastName',':keyword')
            )
            ->setParameter('keyword', $keyword);
        }

        return $qb->getQuery();
    }
}
