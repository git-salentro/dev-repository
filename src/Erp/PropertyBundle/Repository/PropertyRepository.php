<?php

namespace Erp\PropertyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Model\PropertyFilter;
use Erp\UserBundle\Entity\User;

/**
 * PropertyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PropertyRepository extends EntityRepository
{
    const ID_SEPARATOR = '~';

    /**
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return array
     */
    public function findAvailable(User $user = null)
    {
        $result = [];
        if ($user && $user->hasRole(User::ROLE_TENANT)) {
            $result = $this->findAvailableByUser($user);
        }

        if (!$result) {
            $result = $this->getQbForAvailableByDate();
        }

        return $result;
    }

    /**
     * Get QueryBuilder for available properties by updated date
     *
     * @param int $cntNeedle
     * @param array $notInclude
     *
     * @return array
     */
    public function getQbForAvailableByDate($cntNeedle = Property::LIMIT_AVAILABLE_PER_PAGE, array $notInclude = [])
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('pr')
            ->from($this->_entityName, 'pr')
            ->where('pr.status = :status')
            ->setParameter('status', Property::STATUS_AVAILABLE)
            ->orderBy('pr.updatedDate', 'DESC')
            ->setMaxResults($cntNeedle);

        if ($notInclude) {
            foreach ($notInclude as $exception) {
                $ids[] = $exception->getId();
            }
            $qb = $qb->andWhere($qb->expr()->notIn('pr.id', $ids));
        }
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function getQueryBuilderByUser(User $user)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb
            ->where('p.user = :user')
            ->setParameter('user', $user);
    }

    /**
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return array
     */
    public function findAvailableByUser(User $user)
    {
        $result = $this->getAvailableByUserPostalCode($user);
        $cntZipAvailable = count($result);

        if ($cntZipAvailable < Property::LIMIT_AVAILABLE_PER_PAGE) {
            $cntNeedleBuyCity = Property::LIMIT_AVAILABLE_PER_PAGE - $cntZipAvailable;
            $cityAvailable = $this->getAvailableByUserCity($user, $cntNeedleBuyCity, $result);
            $result = array_merge($result, $cityAvailable);

            $cntCityAvailable = count($result);
            if ($cntCityAvailable < Property::LIMIT_AVAILABLE_PER_PAGE) {
                $cntNeedleBuyState = Property::LIMIT_AVAILABLE_PER_PAGE - $cntCityAvailable;
                $stateAvailable = $this->getAvailableByUserState($user, $cntNeedleBuyState, $result);
                $result = array_merge($result, $stateAvailable);

                $availableCnt = count($result);
                if ($availableCnt < Property::LIMIT_AVAILABLE_PER_PAGE) {
                    $needleAvailable = Property::LIMIT_AVAILABLE_PER_PAGE - $availableCnt;
                    $availableByDate = $this->getQbForAvailableByDate($needleAvailable, $result);
                    $result = array_merge($result, $availableByDate);
                }
            }
        }

        return $result;
    }

    /**
     * Get Available properties by user zip
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return array
     */
    public function getAvailableByUserPostalCode(User $user)
    {
        $qb = $this->getQbForAvailableByUserZip($user);
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Get Available properties by user city
     *
     * @param User $user
     * @param int $cntNeedleBuyCity
     * @param array $notInclude
     *
     * @return array
     */
    public function getAvailableByUserCity(User $user, $cntNeedleBuyCity, array $notInclude = [])
    {
        $qb = $this->getQbForAvailableByUserCity($user, $notInclude, $cntNeedleBuyCity);
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Get Available properties by user state
     *
     * @param User $user
     * @param array $result
     * @param int $cntNeedleBuyCity
     *
     * @return array
     */
    public function getAvailableByUserState(User $user, $cntNeedleBuyCity, array $result = [])
    {
        $qb = $this->getQbForAvailableByUserState($user, $result, $cntNeedleBuyCity);
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @param PropertyFilter $propertyFilter
     *
     * @return array
     */
    public function getBySearchParams(PropertyFilter $propertyFilter)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('pr')
            ->from($this->_entityName, 'pr')
            ->where('pr.status = :status')
            ->setParameter('status', Property::STATUS_AVAILABLE);
        $qb = $this->modifyQbByParams($qb, $propertyFilter);

        $paginator = new Paginator($qb);
        $propertyFilter->setCountProperties(count($paginator));

        $paginator->getQuery()
            ->setFirstResult(Property::LIMIT_SEARCH_PER_PAGE * ($propertyFilter->getPage() - 1))
            ->setMaxResults(Property::LIMIT_SEARCH_PER_PAGE);

        $result = $paginator->getQuery()->getResult();

        return $result;
    }

    public function addIdentifiersToQueryBuilder(QueryBuilder $qb, array $idx)
    {
        if (!$idx) {
            return;
        }

        $fieldNames = $this->getClassMetadata()->getIdentifierFieldNames();

        $prefix = uniqid();
        $sqls = array();
        foreach ($idx as $pos => $id) {
            $ids = explode(self::ID_SEPARATOR, $id);

            $ands = array();
            foreach ($fieldNames as $posName => $name) {
                $parameterName = sprintf('field_%s_%s_%d', $prefix, $name, $pos);
                $ands[] = sprintf('%s.%s = :%s', $qb->getRootAlias(), $name, $parameterName);
                $qb->setParameter($parameterName, $ids[$posName]);
            }

            $sqls[] = implode(' AND ', $ands);
        }

        $qb->andWhere(sprintf('( %s )', implode(' OR ', $sqls)));
    }

    /**
     * Return property by user
     *
     * @param User $user
     * @param      $propertyId
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPropertyByUser(User $user, $propertyId)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('pr')
            ->from($this->_entityName, 'pr')
            ->where('pr.id = :propertyId')
            ->andWhere('pr.status <> :status')
            ->andWhere('pr.user = :user')
            ->setParameter('propertyId', $propertyId)
            ->setParameter('status', Property::STATUS_DELETED)
            ->setParameter('user', $user);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    public function getPropertiesQuery(User $user, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null)
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('p')
            ->join(\Erp\PropertyBundle\Entity\PropertyRentHistory::class, 'prh', Expr\Join::WITH, 'p.id=prh.property')
            ->where('p.user = :user')
            ->setParameter('user', $user);

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('prh.createdAt', ':dateFrom', ':dateTo'));
                $qb->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('prh.createdAt > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        if ($type) {
            $qb->andWhere(
                $qb->expr()->in(
                    'p.status',
                    $type
                )
            );
        }

        return $qb->getQuery();
    }

    public function getScheduledPropertiesForPayment()
    {
        //TODO Optimize. Get rid of hydration
        $yesterday = (new \DateTime())->modify('-1 day');
        $yesterdayDay = $yesterday->format('j');

        $qb = $this->createQueryBuilder('p')
            ->select('p', 'ps', 'tu')
            ->join('p.settings', 'ps')
            ->join('p.tenantUser', 'tu')
            ->where('ps.dayUntilDue = :yesterdayDay')
            ->setParameter('yesterdayDay', $yesterdayDay);

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function getPropertiesByRentDueDate($rentDueDate)
    {
        $qb = $this->createQueryBuilder('p')
            ->select('tu')
            ->join('p.settings', 'ps')
            ->join('p.tenantUser', 'tu')
            ->join('p.user', 'u')
            ->where('ps.dayUntilDue = :dayUntilDue')
            ->setParameter('dayUntilDue', $rentDueDate);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Change QueryBuilder by params
     *
     * @param QueryBuilder   $qb
     * @param PropertyFilter $propertyFilter
     *
     * @return QueryBuilder
     */
    protected function modifyQbByParams(QueryBuilder $qb, PropertyFilter $propertyFilter)
    {
        if ($propertyFilter->getState()) {
            $qb->andWhere('pr.stateCode = :state')
                ->setParameter('state', $propertyFilter->getState());
        }

        if ($propertyFilter->getCityId()) {
            $city = $this->getEntityManager()->getRepository('ErpCoreBundle:City')->find(
                $propertyFilter->getCityId()
            );
            if ($city) {
                $qb->andWhere('pr.city = :city')
                    ->setParameter('city', $city);
            }
        }

        if ($propertyFilter->getAddress()) {
            $qb->andWhere('LOWER(pr.address) LIKE :address')
                ->setParameter('address', '%' . strtolower($propertyFilter->getAddress()) . '%');
        }

        $zip = $propertyFilter->getZip();
        if ($zip && ctype_digit($zip)) {
            $qb->andWhere('pr.zip = :zip')
                ->setParameter('zip', $zip);
        }

        if ($propertyFilter->getPriceMin() && $propertyFilter->getPriceMax()) {
            $qb->andWhere('pr.price BETWEEN :priceMin AND :priceMax')
                ->setParameter('priceMin', $propertyFilter->getPriceMin())
                ->setParameter('priceMax', $propertyFilter->getPriceMax());
        } else {
            if ($propertyFilter->getPriceMin()) {
                $qb->andWhere('pr.price >= :price')
                    ->setParameter('price', $propertyFilter->getPriceMin());
            }

            if ($propertyFilter->getPriceMax()) {
                $qb->andWhere('pr.price <= :price')
                    ->setParameter('price', $propertyFilter->getPriceMax());
            }
        }

        if ($propertyFilter->getBathrooms()) {
            $qb->andWhere('pr.ofBaths = :ofBaths')
                ->setParameter('ofBaths', $propertyFilter->getBathrooms());
        }

        if ($propertyFilter->getBedrooms()) {
            $qb->andWhere('pr.ofBeds = :ofBeds')
                ->setParameter('ofBeds', $propertyFilter->getBedrooms());
        }

        $squareFootage = $propertyFilter->getSquareFootage();
        if ($squareFootage && ctype_digit($squareFootage)) {
            $qb->andWhere('pr.squareFootage >= :squareFootage')
                ->setParameter('squareFootage', $squareFootage);
        }

        $squareFootage = $propertyFilter->getSquareFootage();
        if ($squareFootage && ctype_digit($squareFootage)) {
            $qb->andWhere('pr.squareFootage >= :squareFootage')
                ->setParameter('squareFootage', $squareFootage);
        }

        $order = $propertyFilter->getOrder();

        if ($order) {
            $parts = explode('_', $order);
            $qb->orderBy('pr.' . $parts[0], $parts[1]);
        }

        return $qb;
    }

    /**
     * Get QueryBuilder for available properties by user zip
     *
     * @param User $user
     * @param int  $maxResults
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQbForAvailableByUserZip(User $user, $maxResults = Property::LIMIT_AVAILABLE_PER_PAGE)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('pr')
            ->from($this->_entityName, 'pr')
            ->where('pr.zip = :zip')
            ->andWhere('pr.status = :status')
            ->setParameter('zip', $user->getPostalCode())
            ->setParameter('status', Property::STATUS_AVAILABLE)
            ->orderBy('pr.updatedDate', 'DESC')
            ->setMaxResults($maxResults);

        return $qb;
    }

    /**
     * Get QueryBuilder for available properties by user city
     *
     * @param User  $user
     * @param array $exceptions
     * @param int   $maxResults
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQbForAvailableByUserCity(
        User $user,
        array $exceptions = [],
        $maxResults = Property::LIMIT_AVAILABLE_PER_PAGE
    ) {
        $qb = $this->_em->createQueryBuilder()
            ->select('pr')
            ->from($this->_entityName, 'pr')
            ->where('pr.city = :city')
            ->andWhere('pr.status = :status')
            ->setParameter('city', $user->getCity())
            ->setParameter('status', Property::STATUS_AVAILABLE)
            ->orderBy('pr.updatedDate', 'DESC')
            ->setMaxResults($maxResults);

        if ($exceptions) {
            foreach ($exceptions as $exception) {
                $ids[] = $exception->getId();
            }

            $qb = $qb->andWhere($qb->expr()->notIn('pr.id', $ids));
        }

        return $qb;
    }

    /**
     * Get QueryBuilder for available properties by user state
     *
     * @param User  $user
     * @param array $exceptions
     * @param int   $maxResults
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQbForAvailableByUserState(
        User $user,
        array $exceptions = [],
        $maxResults = Property::LIMIT_AVAILABLE_PER_PAGE
    ) {
        $qb = $this->_em->createQueryBuilder()
            ->select('pr')
            ->from($this->_entityName, 'pr')
            ->where('pr.stateCode = :stateCode')
            ->andWhere('pr.status = :status')
            ->setParameter('stateCode', $user->getState())
            ->setParameter('status', Property::STATUS_AVAILABLE)
            ->orderBy('pr.updatedDate', 'DESC')
            ->setMaxResults($maxResults);

        if ($exceptions) {
            foreach ($exceptions as $exception) {
                $ids[] = $exception->getId();
            }

            $qb = $qb->andWhere($qb->expr()->notIn('pr.id', $ids));
        }

        return $qb;
    }

    public function getDebtors(User $user)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p, tu')
            ->join('p.user', 'u')
            ->join('p.tenantUser', 'tu')
            ->join('tu.rentPaymentBalance', 'rpb')
            ->where('p.user = :user')
            ->andWhere('rpb.balance < 0')
            ->setParameter('user', $user);

        return $qb->getQuery()
            ->getResult();
    }

    public function getPropertiesListExceptCurrent(Property $property, User $user)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('p')
            ->where('p.user = :user')
            ->andWhere('p.status != :status')
            ->setParameter('status','deleted')
            ->andWhere($qb->expr()->neq('p.id', $property->getId()))
            ->setParameter('user', $user);

        return $qb->getQuery()->getResult();
    }
}
