<?php

namespace Erp\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Erp\UserBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ManagerFlagAssignFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $user */
        $manager = $userManager->findUserByEmail('tonystark@test.com'); //manager
        $manager->setManager($this->getReference('johndoe@test.com'));//landlord
        $userManager->updateUser($manager);

        /** @var User $user */
        $landlord = $userManager->findUserByEmail('johndoe@test.com'); //landlord
        $landlord->setManager($this->getReference('peterparker@test.com'));//landlord
        $userManager->updateUser($landlord);
    }

    public function getDependencies()
    {
        return array(
            UserFixture::class,
        );
    }
}