<?php

namespace Erp\UserBundle\DataFixtures\ORM;

use Erp\UserBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        $this->createLandlord();
        $this->createTenant();
    }

    private function createLandlord()
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $settings = $this->container->get('erp.users.user.service')->getSettings();

        /** @var User $user */
        $user = $userManager->createUser();
        $user->addRole(User::ROLE_LANDLORD);
        $user
            ->setCompanyName('My landlord Company')
            ->setFirstName('Tony')
            ->setLastName('Stark')
            ->setPhone('555-555-5555')
            ->setAddressOne('Address One')
            ->setPostalCode('11111')
            ->setEmail('tonystark@test.com')
            ->setPlainPassword('qweASD123')
            ->setEnabled(true)
            ->setUsername($user->getEmail())
            ->setStatus(User::STATUS_ACTIVE)
            ->setSettings(array_keys($settings))
            ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
            ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
            ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
            ->setIsPrivatePaySimple(0)
            ->setIsApplicationFormCounterFree(1)
            ->setIsPropertyCounterFree(1)
            ->setIsTermOfUse(true);

        $userManager->updateUser($user);

        $this->addReference('tonystark@test.com', $user);
    }

    private function createTenant()
    {
        $userManager = $this->container->get('fos_user.user_manager');

        /** @var User $user */
        $user = $userManager->createUser();
        $user->addRole(User::ROLE_TENANT);
        $user
            ->setCompanyName('Tenant')
            ->setFirstName('Peter')
            ->setLastName('Parker')
            ->setPhone('555-555-5555')
            ->setAddressOne('Address One')
            ->setPostalCode('11111')
            ->setEmail('peterparker@test.com')
            ->setPlainPassword('qweASD123')
            ->setEnabled(true)
            ->setUsername($user->getEmail())
            ->setStatus(User::STATUS_ACTIVE)
            ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
            ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
            ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
            ->setIsPrivatePaySimple(0)
            ->setIsTermOfUse(true);

        $userManager->updateUser($user);

        $this->addReference('peterparker@test.com', $user);
    }
}