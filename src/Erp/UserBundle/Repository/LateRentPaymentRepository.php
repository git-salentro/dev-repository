<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

class LateRentPaymentRepository extends EntityRepository
{
    public function getLatePayments(User $user)
    {
        $qb = $this->createQueryBuilder('lrp')
            ->select('lrp, tu')
            ->join('lrp.user', 'tu')
            ->join('tu.tenantProperty', 'p')
            ->join('p.user', 'u')
            ->join('tu.rentPaymentBalance', 'rpb')
            ->andWhere('p.user = :user')
            ->andWhere('rpb.debtStartAt <= lrp.createdAt')
            ->andWhere('rpb.balance < 0')
            ->setParameter('user', $user);

        return $qb->getQuery()
            ->getResult();
    }
}