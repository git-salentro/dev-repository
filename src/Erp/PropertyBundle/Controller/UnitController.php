<?php

namespace Erp\PropertyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Entity\StripeSubscription;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertySettings;
use Erp\PropertyBundle\Entity\Unit;
use Erp\PropertyBundle\Form\Type\UnitType;
use Erp\StripeBundle\Helper\ApiHelper;
use Erp\UserBundle\Entity\User;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;

class UnitController extends Controller
{
    public function buyAction(Request $request)
    {
        $form = $this->createForm(new UnitType());
        /** @var User $user */
        $user = $this->getUser();

        //TODO Store in db
        $settings = [
            [
                'min' => 1,
                'max' => 1,
                'amount' => 99,
            ],
            [
                'min' => 2,
                'max' => 29,
                'amount' => 20,
            ],
            [
                'min' => 30,
                'max' => 100000,
                'amount' => 15,
            ],
        ];
        $template = 'ErpPropertyBundle:Unit:form.html.twig';
        $templateParams = [
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
            'current_year_price' => 0,
            'total_price' => 0,
            'existing_unit_count' => 0,
            'settings' => $settings,
        ];
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = $user->getStripeCustomer();

        if (!$stripeCustomer) {
            if ('POST' === $request->getMethod()) {
                $templateParams['errors'] = 'Please, add bank account.';
            }

            return $this->render($template, $templateParams);
        }

        $form->handleRequest($request);

        $stripeSubscription = $stripeCustomer->getStripeSubscription();
        
        $existingUnitCount = $user->getProperties()->count();

        $templateParams['existing_unit_count'] = $existingUnitCount;
        $templateParams['current_year_price'] = $stripeSubscription ? $stripeSubscription->getQuantity() : 0;
        $templateParams['total_price'] = $stripeSubscription ? $stripeSubscription->getQuantity() : 0;

        if (!$form->isValid()) {
            return $this->render($template, $templateParams);
        }

        //TODO Refactoring
        /** @var Unit $unit */
        $unit = $form->getData();
        $quantity = $unit->getQuantity();
        $quantity = $quantity + $existingUnitCount;
        $newPlanQuantity = 0;
        // TODO Create calculate service.
        foreach ($settings as $setting) {
            for ($i = $setting['min']; $i <= $quantity; $i++) {
                $newPlanQuantity += $setting['amount'];

                if ($i == $setting['max']) {
                    break;
                }
            }
        }

        $apiManager = $this->get('erp_stripe.entity.api_manager');
        $em = $this->getDoctrine()->getManager();

        if (!$stripeSubscription) {
            $arguments = [
                'params' => [
                    'customer' => $stripeCustomer->getCustomerId(),
                    'items' => [
                        [
                            'plan' => StripeSubscription::BASE_PLAN_ID,
                            'quantity' => $newPlanQuantity,
                        ],
                    ],
                    'metadata' => [
                        'unit_quantity' => $quantity,
                    ],
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'create', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
            /** @var Subscription $subscription */
            $subscription = $response->getContent();

            $stripeSubscription = new StripeSubscription();
            $stripeSubscription->setSubscriptionId($subscription['id'])
                ->setQuantity($newPlanQuantity)
                ->setStripeCustomer($stripeCustomer);
            $stripeCustomer->setStripeSubscription($stripeSubscription);

            $em->persist($stripeCustomer);
        } else {
            $arguments = [
                'id' => $stripeSubscription->getSubscriptionId(),
                'params' => [
                    'quantity' => $newPlanQuantity,
                    'metadata' => [
                        'unit_quantity' => $quantity,
                    ],
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'update', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            $amount = $newPlanQuantity - $stripeSubscription->getQuantity();
            $arguments = [
                'params' => [
                    'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                    'customer' => $stripeCustomer->getCustomerId(),
                    'currency' => StripeCustomer::DEFAULT_CURRENCY,
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Charge', 'create', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            $stripeSubscription->setQuantity($newPlanQuantity);
            $em->persist($stripeSubscription);
        }

        $prototype = new Property();
        for ($i=1; $i<=$quantity; $i++) {
            $property = clone $prototype;
            $property->setUser($user);

            $prototype->setSettings(new PropertySettings());

            $em->persist($property);
        }

        $em->flush();

        return $this->redirectToRoute('erp_property_listings_all');
    }
}
