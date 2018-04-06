<?php

namespace Erp\PropertyBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertyRentHistory;
use Doctrine\Common\Persistence\ManagerRegistry;

class PropertyEntityListener
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Property $property
     */
    public function postPersist(Property $property)
    {
        $this->createHistoryRecord($property);
    }

    /**
     * @param Property $property
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(Property $property, PreUpdateEventArgs $args)
    {
        $changeSet = $args->getEntityChangeSet();

        //TODO: rewrite this part. It creates conflict and do not allow to REMOVE property
//        if (!empty($changeSet[Property::FILED_STATUS])) {
//            $this->createHistoryRecord($property);
//        }

    }

    /**
     * @param Property $property
     */
    private function createHistoryRecord(Property $property)
    {
        $em = $this->registry->getManagerForClass(PropertyRentHistory::class);
        $propertyRentHistory = new PropertyRentHistory();
        $propertyRentHistory->setStatus($property->getStatus());

        $property->addHistory($propertyRentHistory);

        $em->persist($propertyRentHistory);
        $em->flush();
    }
}