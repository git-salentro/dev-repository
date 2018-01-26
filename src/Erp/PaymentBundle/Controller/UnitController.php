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
        $userUnitSettingsManager = $this->get('erp.payment.entity.unit_settings_manager');
        $quantity = $userUnitSettingsManager->getQuantity($unit);
        $apiManager = $this->get('erp_stripe.entity.api_manager');

        if (!$hasSubscriptions) {
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
                        'unit_count' => $unit->getCount(),
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

            $newQuantity = $subscription->quantity + $quantity;
            $count = $subscription->metadata['unit_count'] + $unit->getCount();

            $arguments = [
                'id' => $stripeSubscription->getSubscriptionId(),
                'params' => [
                    'quantity' => $quantity,
                    'metadata' => [
                        'unit_count' => $count,
                    ],
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'update', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            $arguments = [
                'params' => [
                    'amount' => $newQuantity,
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

        $stripeSubscription->setQuantity($quantity);
        $this->em->persist($stripeSubscription);

        $count = $unit->getCount();
        $prototype = new Property();
        for ($i=1; $i<=$count; $i++) {
            $property = clone $prototype;
            $property->setUser($user);

            $prototype->setSettings(new PropertySettings());

            $this->em->persist($property);
        }

        $this->em->flush();

        return $this->redirectToRoute('erp_property_listings_all');
    }
}
