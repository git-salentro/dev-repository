<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Entity\StripeRecurringPayment;
use Erp\PaymentBundle\Entity\Unit;
use Erp\PaymentBundle\Form\Type\UnitType;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\User;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;

class UnitController extends BaseController
{
    public function buyAction(Request $request)
    {
        $form = $this->createForm(new UnitType());
        $form->handleRequest($request);
        /** @var User $user */
        $user = $this->getUser();

        $template = 'ErpPaymentBundle:Unit:form.html.twig';
        $templateParams = [
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
        ];

        if (!$form->isValid() && !$form->isSubmitted()) {
            return $this->render($template, $templateParams);
        }
        /** @var Unit $unit */
        $unit = $form->getData();
        $userUnitSettingsManager = $this->get('erp.payment.entity.unit_settings_manager');
        $quantity = $userUnitSettingsManager->getQuantity($unit);
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomers()->first();

        if (!$stripeCustomer) {
            $templateParams['errors'] = 'Please, add bank account.';
            return $this->render($template, $templateParams);
        }

        $stripeRecurringPayments = $stripeCustomer->getStripeRecurringPayments();

        $subscriptionManager = $this->get('erp.payment.stripe.manager.subscription_manager');

        if ($stripeRecurringPayments->isEmpty()) {
            $response = $subscriptionManager->create([
                'customer' => $stripeCustomer->getCustomerId(),
                'items' => [
                    [
                        'plan' => StripeRecurringPayment::BASE_PLAN_ID,
                        'quantity' => $quantity,
                    ],
                ],
                'metadata' => [
                    'unit_count' => $unit->getCount(),
                ],
            ]);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
            /** @var Subscription $subscription */
            $subscription = $response->getContent();

            $stripeRecurringPayment = new StripeRecurringPayment();
            $stripeRecurringPayment->setSubscriptionId($subscription['id']);

            $stripeCustomer->addStripeRecurringPayment($stripeRecurringPayment);

            $this->em->persist($stripeCustomer);
        } else {
            /** @var StripeRecurringPayment $stripeRecurringPayment */
            $stripeRecurringPayment = $stripeRecurringPayments->last();
            $response = $subscriptionManager->retrieve($stripeRecurringPayment->getSubscriptionId());

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            /** @var Subscription $subscription */
            $subscription = $response->getContent();

            $quantity = $subscription->quantity + $quantity;
            $count = $subscription->metadata['unit_count'];

            $response = $subscriptionManager->update(
                $subscription,
                [
                    'quantity' => $quantity,
                    'metadata' => [
                        'unit_count' => $count + $unit->getCount(),
                    ],
                ]
            );

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
        }

        $count = $unit->getCount();
        $prototype = new Property();
        for ($i=1; $i<=$count; $i++) {
            $property = clone $prototype;
            $property->setUser($user);

            $this->em->persist($property);
        }

        $this->em->flush();

        return $this->redirectToRoute('erp_property_listings_all');
    }
}
