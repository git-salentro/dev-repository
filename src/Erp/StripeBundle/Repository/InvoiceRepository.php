<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\StripeAccount;

class InvoiceRepository extends EntityRepository
{
    public function getGroupedInvoices(StripeAccount $stripeAccount = null, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->select('COUNT(i.id) as gCount, MONTH(i.created) as gMonth, YEAR(i.created) as gYear');

        if ($stripeAccount) {
            $qb->where('i.owner = :owner')
                ->setParameter('owner', $stripeAccount);
        }

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('i.created', ':dateFrom', ':dateTo'))
                    ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('i.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        $qb->groupBy('gYear')
            ->addGroupBy('gMonth');

        return $qb->getQuery()->getResult();
    }
}
