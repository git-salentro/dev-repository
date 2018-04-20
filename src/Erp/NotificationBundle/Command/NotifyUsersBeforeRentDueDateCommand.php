<?php

namespace Erp\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\NotificationBundle\Entity\History;

class NotifyUsersBeforeRentDueDateCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('erp:notification:notify-users-before-rent-due-date')
            ->setDescription('Notify users before rent due date');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $i = 0;
        $k = 0;
        $batchSize = 20;

        $userNotificationEm = $this->getEntityManager(UserNotification::class);
        $propertyEm = $this->getEntityManager(Property::class);
        $historyEm = $this->getEntityManager(History::class);
        $mailer = $this->getContainer()->get('erp_user.mailer.processor');
        $historyManager = $this->getContainer()->get('erp_notification.history_manager');

        if ($iterableResult = $userNotificationEm->getRepository(UserNotification::class)->getPropertiesFromNotificationsIterator()) {
            foreach ($iterableResult as $propertyResult) {
                $i++;
                $data = reset($propertyResult);
                if ($property = $propertyEm->getRepository(Property::class)->find($data['propertyId'])) {
                    $fields = $data;
                    $fields['property'] = $property;
                    $fields['tenant'] = $property->getTenantUser();
                    
                    $history = $historyManager->create($fields);

                    $historyEm->persist($history);

                    if (($k++ % $batchSize) === 0) {
                        $historyEm->flush();
                        $historyEm->clear();
                    }
                }
            }
        }
        $output->writeln($i.' properties.');
    }

    private function getEntityManager($class)
    {
        $repository = $this->getContainer()->get('doctrine')->getManagerForClass($class);

        return $repository;
    }
}
