<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @package Erp\UserBundle\Repository
 */
class UserRepository extends EntityRepository
{
    /**
     * Get users by role
     *
     * @param string      $role
     * @param string|null $otherRole
     *
     * @return array
     */
    public function findByRole($role, $otherRole = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%');

        if ($otherRole) {
            $qb->orWhere('u.roles LIKE :otherRole')
                ->setParameter('otherRole', '%"' . $otherRole . '"%');
        }

        return $qb->getQuery()->getResult();
    }

    public function findByManagers($managers)
    {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->andWhere('u.managers IN (:managers)')
            ->setParameter('managers', $managers);

        return $qb->getQuery()->getResult();
    }

}
