<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

class LateRentPaymentRepository extends EntityRepository
{
    public function getLatePayments(User $user)
    {
        $qb = $this->createQueryBuilder('lrp')
            ->select('lrp, tu, p')
            ->join('lrp.user', 'tu')
            ->join('tu.tenantProperty', 'p')
            ->join('p.user', 'u')
            ->andWhere('p.user = :user')
            ->andWhere('lrp.paid = 0')
            ->setParameter('user', $user);

        return $qb->getQuery()
            ->getResult();
    }
}