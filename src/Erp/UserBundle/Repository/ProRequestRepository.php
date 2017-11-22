<?php

namespace Erp\UserBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\ProRequest;

/**
 * ProRequestRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProRequestRepository extends EntityRepository
{
    /**
     * Get consultants with count refferal landlords by month
     *
     * @return array
     */
    public function getRefferalReportQB()
    {
        $query = $this->_em->createQueryBuilder()
            ->from($this->getEntityName(), 'pr')
            ->innerJoin('pr.proConsultant', 'pc', 'pr.proConsultant = pc.id')
            ->addSelect('pc.email')
            ->addSelect('COUNT(pr.id) as count_landlords')
            ->addSelect('DATE_FORMAT(pr.approvedDate, \'%M\') as month_name')
            ->where('pr.isRefferal = 1')
            ->andWhere('pr.approvedDate IS NOT NULL')
            ->andWhere('pr.status = :status')
            ->addGroupBy('pr.approvedDate, pr.user')
            ->orderBy('MONTH(pr.approvedDate)', 'DESC')
            ->setParameter('status', ProRequest::STATUS_APPROVED);

        return $query;
    }
}
