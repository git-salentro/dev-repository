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


    public function getTransactionsSearchQuery($stripeAccountId = null, $stripeCustomerId = null, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null, $keywords = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.created', 'DESC');
        if ($stripeAccountId) {  //outgoing transaction (account -> customer)
            $qb
                ->andWhere(
                    $qb->expr()->in(
                        't.account',
                        $stripeAccountId
                    )
                );
            if ($stripeCustomerId) {
                $qb
                    ->andWhere(
                        $qb->expr()->in(
                            't.customer',
                            $stripeCustomerId
                        )
                    );
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

        if ($keywords) {
            $words = explode(" ", $keywords);
            foreach ($words as $word) {
                $qb->andWhere('t.status LIKE \'%' . $word . '%\'');
                $qb->orWhere('t.metadata LIKE \'%' . $word . '%\'');
            }
        }



        return $qb->getQuery();
    }
}
