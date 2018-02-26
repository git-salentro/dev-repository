<?php

namespace Erp\PropertyBundle\Command;

use Erp\UserBundle\Entity\LateRentPayment;
use Erp\UserBundle\Entity\RentPaymentBalance;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;

class CheckRentPaymentCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('erp:property:check-rent-payment')
            ->setDescription('Check rent payment');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repository = $em->getRepository(Property::class);
        $properties = $repository->getScheduledPropertiesForPayment();

        $i = 0;
        /** @var Property $property */
        foreach ($properties as $property) {
            $tenant = $property->getTenantUser();
            if (!$tenant) {
                continue;
            }

            if (!$rentPaymentBalance = $tenant->getRentPaymentBalance()) {
                $rentPaymentBalance = new RentPaymentBalance();
                $tenant->setRentPaymentBalance($rentPaymentBalance);
            }

            $propertySettings = $property->getSettings();

            $paymentAmount = $propertySettings->getPaymentAmount();
            $rentPaymentBalanceAmount = $rentPaymentBalance->getBalance();
            $cashBalance = $rentPaymentBalanceAmount - $paymentAmount;
            $lateRentPayment = null;

            if ($rentPaymentBalanceAmount > 0 && $cashBalance < 0) {
                $lateRentPayment = new LateRentPayment();
                $lateRentPayment->setAmount($cashBalance);
            } elseif ($cashBalance < 0) {
                $lateRentPayment = new LateRentPayment();
                $lateRentPayment->setAmount($paymentAmount);
            }

            $tenant->addLateRentPayment($lateRentPayment);

            $rentPaymentBalance->takeMoneyFromBalance($paymentAmount);
            $em->persist($tenant);

            if ((++$i % 20) == 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
    }
}
