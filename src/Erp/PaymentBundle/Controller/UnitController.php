<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Entity\StripeSubscription;
use Erp\PaymentBundle\Entity\Unit;
use Erp\PaymentBundle\Entity\UnitSettings;
use Erp\PaymentBundle\Form\Type\UnitType;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertySettings;
use Erp\UserBundle\Entity\User;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;

class UnitController extends BaseController
{
    public function buyAction(Request $request)
    {
        $form = $this->createForm(new UnitType());
        /** @var User $user */
        $user = $this->getUser();

        $unitSettingsRepository = $this->getDoctrine()->getManagerForClass(UnitSettings::class)->getRepository(UnitSettings::class);
        /** @var UnitSettings $unitSettings */
        $unitSettings = $unitSettingsRepository->getSettings();

        $template = 'ErpPaymentBundle:Unit:form.html.twig';
        $templateParams = [
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
            'hasSubscriptions' => false,
            'unitSettings' => $unitSettings,
        ];
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomer();

        if (!$stripeCustomer) {
            $templateParams['errors'] = 'Please, add bank account.';
            return $this->render($template, $templateParams);
        }

        $stripeSubscriptions = $stripeCustomer->getStripeSubscriptions();
        $hasSubscriptions = !$stripeSubscriptions->isEmpty();
        $templateParams['hasSubscriptions'] = $hasSubscriptions;

        if ($hasSubscriptions) {
            /** @var StripeSubscription $stripeSubscription */
            $stripeSubscription = $stripeSubscriptions->last();
            $templateParams['currentYearPrice'] = $stripeSubscription->getQuantity();
            $templateParams['totalPrice'] = $stripeSubscription->getQuantity();
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render($template, $templateParams);
        }
        /** @var Unit $unit */
        $unit = $form->getData();
        $unitCount = $unit->getCount();
        $unitSettingsManager = $this->get('erp.payment.entity.unit_settings_manager');
        $apiManager = $this->get('erp_stripe.entity.api_manager');

        if (!$hasSubscriptions) {
            $quantity = $unitSettingsManager->getQuantity($unit);
            $arguments = [
                'options' => null,
                'params' => [
                    'customer' => $stripeCustomer->getCustomerId(),
                    'items' => [
                        [
                            'plan' => StripeSubscription::BASE_PLAN_ID,
                            'quantity' => $quantity,
                        ],
                    ],
                    'metadata' => [
                        'unit_count' => $unitCount,
                    ],
                ],
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'create', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
            /** @var Subscription $subscription */
            $subscription = $response->getContent();

            $stripeSubscription = new StripeSubscription();
            $stripeSubscription->setSubscriptionId($subscription['id']);

            $stripeCustomer->addStripeSubscription($stripeSubscription);

            $this->em->persist($stripeCustomer);
        } else {
            /** @var StripeSubscription $stripeSubscription */
            $stripeSubscription = $stripeSubscriptions->last();
            $arguments =  [
                'id' => $stripeSubscription->getSubscriptionId(),
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'retrieve', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
            /** @var Subscription $subscription */
            $subscription = $response->getContent();
            $quantityPerUnit = $unitSettingsManager->getQuantityPerUnit();
            $quantity = $amount = $quantityPerUnit * $unitCount;
            $newQuantity = $subscription->quantity + $quantity;
            $arguments = [
                'id' => $stripeSubscription->getSubscriptionId(),
                'params' => [
                    'quantity' => $newQuantity,
                    'metadata' => [
                        'unit_count' => $subscription->metadata['unit_count'] + $unitCount,
                    ],
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'update', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            $stripeSubscription->setQuantity($newQuantity);
            $this->em->persist($stripeSubscription);

            $arguments = [
                'params' => [
                    'amount' => $amount,
                    'customer' => $stripeCustomer->getCustomerId(),
                    'currency' => StripeCustomer::DEFAULT_CURRENCY,
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Charge', 'create', $arguments);
            //TODO What if occurred an error but subscription was updated?
            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
        }

        $prototype = new Property();
        for ($i=1; $i<=$unitCount; $i++) {
            $property = clone $prototype;
            $property->setUser($user);

            $prototype->setSettings(new PropertySettings());

            $this->em->persist($property);
        }

        $this->em->flush();

        return $this->redirectToRoute('erp_property_listings_all');
    }
}
