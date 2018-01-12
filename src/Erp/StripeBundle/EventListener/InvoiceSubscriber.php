<?php

namespace Erp\StripeBundle\EventListener;

use Erp\StripeBundle\Event\InvoiceEvent;
use Erp\StripeBundle\Entity\Invoice;
use Erp\PaymentBundle\Entity\StripeAccount;

class InvoiceSubscriber extends AbstractSubscriber
{
    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            InvoiceEvent::CREATED => 'onChargeCreated',
        ];
    }

    public function onChargeCreated(InvoiceEvent $event)
    {
        $stripeEvent = $event->getStripeEvent();

        /** @var \Stripe\Invoice $stripeEvent */
        if (!$stripeEvent instanceof \Stripe\Invoice) {
            throw new \InvalidArgumentException('InvoiceSyncer::syncLocalFromStripe() accepts only Stripe\Charge objects as second parameter.');
        }

        $stripeInvoice = $stripeEvent->data->object;

        $invoice = new Invoice();
        $invoice->setAmount($stripeInvoice->amount_due)
            ->setCreated(new \DateTime($stripeInvoice->date));

        if (isset($stripeEvent->account)) {
            $em = $this->registry->getManagerForClass(StripeAccount::class);
            $owner = $em->getRepository(StripeAccount::class)->findOneBy(['accountId' => $stripeEvent->account]);
            $invoice->setOwner($owner);
        }

        $em = $this->registry->getManagerForClass(Invoice::class);

        $em->persist($invoice);
        $em->flush();
    }
}
