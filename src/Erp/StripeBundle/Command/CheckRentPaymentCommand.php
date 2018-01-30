<?php

namespace Erp\StripeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\RentPayment;

class CheckRentPaymentCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('erp:rent-payment:check')
            ->setDescription('Check rent payment');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository(Property::class);
        $properties = $repository->getScheduledPropertiesForPayment();

        $i = 0;
        /** @var Property $property */
        foreach ($properties as $property) {
            $tenant = $property->getTenantUser();
            if (!$tenant) {
                continue;
            }

            $propertySettings = $property->getSettings();
            $rentPayment = $tenant->getRentPayment();
            if (!$rentPayment) {
                $rentPayment = new RentPayment();
                $rentPayment->setUser($tenant);
            }

            $rentPayment->takeMoneyFromBalance($propertySettings->getPaymentAmount());

            $em->persist($rentPayment);

            if ((++$i % 20) == 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
    }

    private function getEntityManager()
    {
        if (!$this->em) {
            $container = $this->getContainer();
            $this->em = $container->get('doctrine')->getManagerForClass(Property::class);
        }

        return $this->em;
    }
}
