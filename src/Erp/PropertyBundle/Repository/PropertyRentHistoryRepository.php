<?php

namespace Erp\PropertyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;
use Doctrine\ORM\Query\Expr;

class PropertyRentHistoryRepository extends EntityRepository
{
    public function getHistory(User $user, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        //TODO Optimize query. Find better solution
        $subQb = $this->createQueryBuilder('prh')
            ->select('MAX(prh.id) as id, (prh.property) as property, YEAR(prh.createdAt) as year, MONTH(prh.createdAt) as month')
            ->join('prh.property', 'p', Expr\Join::WITH)
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->groupBy('property, year, month');

        if ($dateFrom) {
            if ($dateTo) {
                $subQb->andWhere($subQb->expr()->between('prh.createdAt', ':dateFrom', ':dateTo'));
                $subQb->setParameter('dateTo', $dateTo);
            } else {
                $subQb->andWhere('prh.createdAt > :dateFrom');
            }
            $subQb->setParameter('dateFrom', $dateFrom);
        }
        $result = $subQb->getQuery()
            ->getResult();

        if (!$result) {
            return [];
        }

        $qb = $this->createQueryBuilder('prh');
        $qb->select('prh')
            ->andWhere(
                $qb->expr()->in(
                    'prh.id',
                    array_column($result, 'id')
                )
            );

        return $qb->getQuery()
            ->getResult();
    }
}
