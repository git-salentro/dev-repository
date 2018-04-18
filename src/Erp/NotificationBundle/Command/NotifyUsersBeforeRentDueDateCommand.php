<?php

namespace Erp\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;

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

    }

    private function getRepository()
    {
        $container = $this->getContainer();
        $repository = $container->get('doctrine')->getManagerForClass(Property::class)->getRepository(Property::class);

        return $repository;
    }
}