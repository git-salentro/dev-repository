<?php

namespace Erp\NotificationBundle\Repository;

use Erp\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

class UserNotificationRepository extends EntityRepository
{
    public function getAlertByUserAndId($id, User $user)
    {
        $qb = $this->createQueryBuilder('un');
        $qb->select('un')
            ->join('un.property', 'p')
            ->join('p.user', 'u')
            ->where('u = :user')
            ->andWhere('un.id =: id')
            ->setParameter('user', $user)
            ->setParameter('id', $id);

        return $qb->getQuery()
            ->getResult();
    }
}