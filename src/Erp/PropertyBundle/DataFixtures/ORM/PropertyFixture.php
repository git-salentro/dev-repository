<?php

namespace Erp\PropertyBundle\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\User;

class PropertyFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        /** @var User $tenant */
        $tenant = $this->getReference('peterparker@test.com');
        /** @var User $landlord */
        $landlord = $this->getReference('tonystark@test.com');

        $object = new Property();
        $object->setTenantUser($tenant)
            ->setName('Test Property')
            ->setUser($landlord)
            ->setStatus(Property::STATUS_DRAFT);

        $tenant->setTenantProperty($object);
        $landlord->addProperty($object);

        $manager->persist($tenant);
        $manager->persist($object);
        $manager->flush();
    }
}