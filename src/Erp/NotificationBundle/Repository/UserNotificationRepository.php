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

    public function getPropertiesFromUserNotiticationIterator()
    {
        return $this->createQueryBuilder('un')
            ->distinct()
            ->select('un.id AS userNotificationId')
            ->addSelect('un.sendAlertAutomatically')
            ->addSelect('un.sendNotificationAutomatically')
            ->addSelect('p.id AS propertyId')
            ->addSelect('t.id as templateId')
            ->addSelect('t.type as type')
            ->addSelect('t.title as title')
            ->join('un.template', 't')
            ->join('un.properties', 'p')
            ->join('p.settings', 'ps', 'WITH', 'ps.dayUntilDue IS NOT NULL')
            ->andWhere('p.tenantUser IS NOT NULL')
            ->andWhere('p.status != :status')
            ->setParameter('status', 'deleted');
    }

    public function getPropertiesFromNotificationsIterator()
    {
        return $this->getPropertiesFromUserNotiticationIterator()
            ->leftJoin('un.notifications', 'n', 'WITH', '(ps.dayUntilDue - DAY(CURRENT_DATE())) = n.daysBefore')
            // TODO: refactor this to more `doctrine` way
            ->andWhere('
                (
                    n.id IS NULL AND
                    un.sendNotificationAutomatically = 1 AND
                    (ps.dayUntilDue - DAY(CURRENT_DATE())) = 0
                ) OR (
                    n.id IS NOT NULL AND
                    un.sendNotificationAutomatically = 0
                )')
            ->addSelect('n.id AS notificationId')
            ->addSelect('(ps.dayUntilDue - DAY(CURRENT_DATE())) AS calculatedDaysBefore')
            ->getQuery()->iterate();
    }

    public function getPropertiesFromAlertsIterator()
    {
        // TODO: add check for last payment date
        return $this->getPropertiesFromUserNotiticationIterator()
            ->leftJoin('un.alerts', 'a', 'WITH', '(DAY(CURRENT_DATE()) - ps.dayUntilDue) = a.daysAfter')
            // TODO: refactor this to more `doctrine` way
            ->andWhere('
                (
                    a.id IS NULL AND
                    un.sendAlertAutomatically = 1
                    AND (ps.dayUntilDue - DAY(CURRENT_DATE())) = 0
                ) OR (
                    a.id IS NOT NULL AND
                    un.sendAlertAutomatically = 0
                )')
            ->addSelect('a.id AS alertId')
            ->addSelect('(DAY(CURRENT_DATE()) - ps.dayUntilDue) AS calculatedDaysAfter')
            // ->andWhere('p.lastPaymentDate < CURRENT_DATE()')
            ->getQuery()->iterate();
    }
}
