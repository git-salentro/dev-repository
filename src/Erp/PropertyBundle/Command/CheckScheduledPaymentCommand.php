<?php

namespace Erp\PropertyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\StripeBundle\Helper\ApiHelper;

class CheckScheduledPaymentCommand extends ContainerAwareCommand
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
            ->setName('erp:property:check-scheduled-payment')
            ->setDescription('Charge Tenants');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager();
        $recurringPaymentRepository = $em->getRepository(ScheduledRentPayment::class);

        $scheduledRentPayments = $recurringPaymentRepository->getScheduledRecurringPayments();
        $scheduledSinglePayments = $recurringPaymentRepository->getScheduledSinglePayments();

        $this->makePayment($scheduledRentPayments);
        $this->makePayment($scheduledSinglePayments);
    }

    private function makePayment(array $payments)
    {
        $container = $this->getContainer();
        $logger = $container->get('logger');
        $apiManager = $container->get('erp_stripe.entity.api_manager');
        $em = $this->getEntityManager();

        $i = 0;
        /** @var ScheduledRentPayment $payment */
        foreach ($payments as $payment) {
            $arguments = [
                'params' => [
                    //TODO Refactoring amount in payRentAction form
                    'amount' => ApiHelper::convertAmountToStripeFormat($payment->getAmount()),
                    'currency' => StripeCustomer::DEFAULT_CURRENCY,
                    'customer' => $payment->getCustomer()->getCustomerId(),
                ],
                'options' => [
                    'stripe_account' => $payment->getAccount()->getAccountId()
                ]
            ];
            $response = $apiManager->callStripeApi('\Stripe\Charge', 'create', $arguments);

            if (!$response->isSuccess()) {
                $status = ScheduledRentPayment::STATUS_FAILURE;
                $logger->warning(json_encode($response->getErrorMessage()));
            } else {
                $status = ScheduledRentPayment::STATUS_SUCCESS;
            }

            $payment->setStatus($status);

            if ($payment->isRecurring()) {
                $startPaymentAt = (\DateTimeImmutable::createFromMutable($payment->getStartPaymentAt()));
                $nextPaymentAt = $status === ScheduledRentPayment::STATUS_FAILURE ?
                    $startPaymentAt->modify('+1 day') :
                    $nextPaymentAt =$startPaymentAt->modify('+1 month');

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
            $this->em = $container->get('doctrine')->getManagerForClass(ScheduledRentPayment::class);
        }

        return $this->em;
    }
}
