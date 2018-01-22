<?php

namespace Erp\PropertyBundle\EventListener;

use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertyRentHistory;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Event\LifecycleEventArgs;
class PropertyEntityListener
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function postPersist(Property $property)
    {
        $this->createHistoryRecord($property);
    }

    public function postUpdate(Property $property, LifecycleEventArgs $args)
    {
        $this->createHistoryRecord($property);
    }

    private function createHistoryRecord(Property $property)
    {
        $em = $this->registry->getManagerForClass(PropertyRentHistory::class);
        $propertyRentHistory = new PropertyRentHistory();
        $propertyRentHistory->setStatus($property->getStatus())
            ->setCreatedAt(new \DateTime());

        $property->addHistory($propertyRentHistory);

        $em->persist($propertyRentHistory);
        $em->flush();
    }
}