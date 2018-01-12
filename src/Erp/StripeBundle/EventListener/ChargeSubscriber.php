<?php

namespace Erp\StripeBundle\EventListener;

use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Event\ChargeEvent;
use Erp\PaymentBundle\Entity\StripeAccount;
use Stripe\Charge;

class ChargeSubscriber extends AbstractSubscriber
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ChargeEvent::SUCCEEDED => 'onChargeSucceeded',
        ];
    }

    public function onChargeSucceeded(ChargeEvent $event)
    {
        $stripeEvent = $event->getStripeEvent();

        /** @var Charge $stripeEvent */
        if (!$stripeEvent instanceof Charge) {
            throw new \InvalidArgumentException('ChargeSyncer::syncLocalFromStripe() accepts only Stripe\Charge objects as second parameter.');
        }

        $stripeCharge = $stripeEvent->data->object;

        $transaction = new Transaction();

        $transaction->setAmount($stripeCharge->amount)
            ->setCurrency($stripeCharge->currency)
            ->setCreated((new \DateTime())->setTimestamp($stripeCharge->created))
            ->setType(Transaction::TYPE_CHARGE);


        if (isset($stripeEvent->account)) {
            $em = $this->registry->getManagerForClass(StripeAccount::class);
            $owner = $em->getRepository(StripeAccount::class)->findOneBy(['accountId' => $stripeEvent->account]);
            $transaction->setOwner($owner);
        }

        $em = $this->registry->getManagerForClass(Transaction::class);

        $em->persist($transaction);
        $em->flush();
    }
}
