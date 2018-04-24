<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;
use Erp\PaymentBundle\Entity\StripeCustomer;

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
     * @param string $role
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

    public function getLandlords(User $user)
    {
        $qb = $this->createQueryBuilder('u');
        $qb->select('u')
            ->where('u.manager = :manager')
            ->setParameter('manager', $user);

        return $qb->getQuery()
            ->getResult();
    }

    public function findCustomerID($user_id)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s')
            ->from('Erp\PaymentBundle\Entity\StripeCustomer', 's')
            ->where('s.user = :user_id')
            ->setParameter('s.user', $user_id);

        return $qb->getQuery()->getResult();
    }
}
