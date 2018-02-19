<?php

namespace Erp\PropertyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

class LandlordRepository extends EntityRepository
{
    /**
     * @param \Erp\UserBundle\Entity\User $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getByManagerQB($user)
    {
        $qb = $this->createQueryBuilder('o');
        $qb->join('o.user', 'u');
        $qb->andWhere('o.user = :user');
        $qb->setParameter('user', $user);

        return $qb;
    }
}
