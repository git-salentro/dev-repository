<?php

namespace Erp\CoreBundle\DataFixtures\ORM;

use Erp\CoreBundle\Entity\EmailNotification;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class EmailNotificationsFixture extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /* @var \Erp\CoreBundle\Services\EmailNotificationManager $manager */
        $this->manager = $manager;
        $this->createEmailNotifications();
    }

    private function createEmailNotifications()
    {
        $this->createEmailNotification('service','SERVICE_REQUESTS','Service Request','eRentPay - New Service Request','','{"0":"#url#"}','Click to see','You have received a new service request from Tenant!');
        $this->createEmailNotification('forum','FORUM_TOPICS','Forum message','eRentPay - New Forum Message','','{"0":"#url#"}','Click to see','There is a new message on Forum!');
        $this->createEmailNotification('profile','PROFILE_MESSAGES','Profile messages','eRentPay - New Profile Message','','{"0":"#url#"}','Click to see','You have received a new message from Tenant.');
    }

    protected function createEmailNotification($referenceName, $type, $name, $subject, $body, $tokens, $button, $title)
    {
        $notification = new EmailNotification();
        $this->manager->refresh($notification);
        $notification->setType($type);
        $notification->setName($name);
        $notification->setSubject($subject);
        $notification->setBody($body);
        $notification->setTokens($tokens);
        $notification->setButton($button);
        $notification->setTitle($title);
        $this->manager->persist($notification);
        $this->manager->flush();
        if (trim($referenceName)) {
            $this->setReference(sprintf('notification:%s', trim($referenceName)), $notification);
        }
    }





}
