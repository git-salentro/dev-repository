<?php

namespace Erp\PaymentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PaymentBundle\Entity\StripeRecurringPayment;

class CheckRecurringPaymentCommand extends ContainerAwareCommand
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
            ->setName('erp:payment:charge')
            ->setDescription('Charge Tenants');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager();
        $recurringPaymentRepository = $em->getRepository(StripeRecurringPayment::class);

        $scheduledRecurringPayments = $recurringPaymentRepository->getScheduledRecurringPayments();
        $scheduledSinglePayments = $recurringPaymentRepository->getScheduledRecurringPayments();

        $this->makePayment($scheduledRecurringPayments);
        $this->makePayment($scheduledSinglePayments);
    }

    private function makePayment(array $payments)
    {
        $container = $this->getContainer();
        $logger = $container->get('logger');
        $chargeManager = $container->get('erp.payment.stripe.manager.charge_manager');
        $em = $this->getEntityManager();

        $i = 0;
        /** @var StripeRecurringPayment $payment */
        foreach ($payments as $payment) {
            $response = $chargeManager->create(
                [
                    'amount' => $payment->getAmount(),
                    'currency' => 'usd',
                    'source' => $payment->getCustomer()->getId(),
                ],
                [
                    'stripe_account' => $payment->getAccount()->getId(),
                ]
            );

            if ($response->isSuccess()) {
                $status = StripeRecurringPayment::STATUS_FAILURE;
                $logger->warning(json_encode($response->getErrorMessage()));
            } else {
                $status = StripeRecurringPayment::STATUS_SUCCESS;
            }

            $payment->setStatus($status);

            if ($payment->isRecurring()) {
                $startPaymentAt = (\DateTimeImmutable::createFromMutable($payment->getStartPaymentAt()));
                if ($status == StripeRecurringPayment::STATUS_SUCCESS) {
                    $nextPaymentAt = $startPaymentAt->modify('+1 day');
                } else {
                    $nextPaymentAt = $startPaymentAt->modify('+1 month');
                }
                $payment->setNextPaymentAt((new \DateTime())->setTimestamp($nextPaymentAt->getTimestamp()));
            }

            $em->persist($payment);

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
            $this->em = $container->get('doctrine')->getManagerForClass(StripeRecurringPayment::class);
        }

        return $this->em;
    }
}
