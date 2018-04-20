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
    private function getAlertByUserQuery(User $user)
    {
        $qb = $this->createQueryBuilder('un');

        return $qb->select('un')
            ->join('un.properties', 'p')
            ->where('p.user = :user')
            ->setParameter('user', $user);
    }

    public function getAlertsByUser(User $user)
    {
        return $this->getAlertByUserQuery($user)->getQuery()->getResult();
    }

    public function getPropertiesFromNotificationsIterator()
    {
        return $this->createQueryBuilder('un')
            ->distinct()
            ->select('p.id AS propertyId')
            ->addSelect('t.id as templateId')
            ->addSelect('t.type as type')
            ->addSelect('t.title as title')
            ->join('un.template', 't')
            ->join('un.properties', 'p')
            ->join('p.settings', 'ps', 'WITH', 'ps.dayUntilDue IS NOT NULL')
            ->join('un.notifications', 'n', 'WITH', '(ps.dayUntilDue - DAY(CURRENT_DATE())) = n.daysBefore')
            ->andWhere('p.tenantUser IS NOT NULL')
            ->andWhere('p.status != :status')
            ->setParameter('status', 'deleted')
            ->getQuery()->iterate();
    }
}
