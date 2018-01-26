<?php

namespace Erp\PaymentBundle\Consumer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Erp\StripeBundle\Entity\ApiManager;
use Erp\PaymentBundle\Entity\StripeSubscription;
use Erp\PaymentBundle\Entity\UnitSettings;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Stripe\Subscription;

class UpdateSubscriptionsConsumer implements ConsumerInterface
{
    /**
     * @var ApiManager
     */
    private $manager;

    /**
     * @var ManagerRegistry
     */
    private $registry;

    public function __construct(ManagerRegistry $registry, ApiManager $manager)
    {
        $this->registry = $registry;
        $this->manager = $manager;
    }

    /**
     * @inheritdoc
     */
    public function execute(AMQPMessage $msg)
    {
        $body = $msg->getBody();
        $object = unserialize($body);

        if (!$object instanceof UnitSettings) {
            return;
        }

        $initialQuantity = $object->getInitialQuantity();
        $quantityPerUnit = $object->getQuantityPerUnit();

        $repository = $this->getRepository();
        $subscriptions = $repository->findAll();

        /** @var StripeSubscription $subscription */
        foreach ($subscriptions as $subscription) {
            $response = $this->manager->callStripeApi('\Stripe\Subscription', 'retrieve', ['id' => $subscription->getSubscriptionId()]);
            /** @var Subscription $stripeSubscription */
            $stripeSubscription = $response->getContent();
            $unitCount = $stripeSubscription->metadata['unit_count'];
            /**
             * There quantity = quantityPerUnit * (unitCount - initialUnitCount) + initialQuantity
             */
            $quantity = $quantityPerUnit * ($unitCount - 1) + $initialQuantity;
            $arguments = [
                'id' =>  $subscription->getSubscriptionId(),
                'params' => ['quantity' => $quantity],
                'options' => null,
            ];
            $this->manager->callStripeApi('\Stripe\Subscription', 'update', $arguments);
        }
    }

    private function getRepository()
    {
        return $this->registry->getManagerForClass(StripeSubscription::class)->getRepository(StripeSubscription::class);
    }
}