<?php

namespace Erp\NotificationBundle\Repository;

use Erp\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserNotificationRepository extends EntityRepository
{
    /**
     * @param $id
     * @param User $user
     *
     * @return array
     */
    public function getAlertByUserAndId($id, User $user)
    {
        $qb = $this->getAlertByUserQuery($user);

        $qb->andWhere('un.id =: id')
            ->setParameter('id', $id);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param User $user
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAlertByUserQuery(User $user)
    {
        $qb = $this->createQueryBuilder('un');

        return $qb->select('un')
            ->join('un.property', 'p')
            ->join('p.user', 'u')
            ->where('u = :user')
            ->setParameter('user', $user);
    }
}
