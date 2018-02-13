<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Form\Type\StripeCreditCardType;
use Erp\PaymentBundle\Form\Type\StripeRecurringPaymentType;
use Erp\PaymentBundle\Plaid\Exception\ServiceException;
use Erp\PaymentBundle\Stripe\Model\CreditCard;
use Erp\PaymentBundle\Entity\StripeRecurringPayment;
use Erp\UserBundle\Entity\User;
use Erp\StripeBundle\Form\Type\AccountVerificationType;
use Stripe\Account;
use Stripe\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends BaseController
{
    //TODO Optimize logic
    public function saveCreditCardAction(Request $request)
    {
        $model = new CreditCard();
        $form = $this->createForm(new StripeCreditCardType(), $model);
        $form->handleRequest($request);
        /** @var $user User */
        $user = $this->getUser();

        $template = 'ErpPaymentBundle:Stripe/Forms:cc.html.twig';
        $templateParams = [
            'type' => StripeCustomer::CREDIT_CARD,
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
            'customer' => $user->getStripeCustomer(),
        ];

        if ($form->isValid()) {
            $manager = $user->getTenantProperty()->getUser();
            $managerStripeAccountId = $manager->getStripeAccount()->getAccountId();

            $stripeToken = $model->getToken();
            $options = ['stripe_account' => $managerStripeAccountId];

            $stripeCustomer = $user->getStripeCustomer();
            $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');

            if (!$stripeCustomer) {
                $response = $customerManager->create(
                    [
                        'email' => $user->getEmail(),
                        'source' => $stripeToken,
                    ],
                    $options
                );

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
                /** @var Customer $customer */
                $customer = $response->getContent();

                $stripeCustomer = new StripeCustomer();
                $stripeCustomer->setCustomerId($customer['id']);

                $user->setStripeCustomer($stripeCustomer);

                $this->em->persist($user);
                // Force flush for saving Stripe customer
                $this->em->flush();
            } else {
                $response = $customerManager->retrieve($stripeCustomer->getCustomerId(), $options);

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }

                /** @var Customer $customer */
                $customer = $response->getContent();
                $response = $customerManager->update(
                    $customer,
                    [
                        'source' => $stripeToken,
                    ],
                    $options
                );

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
            }

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        return $this->render($template, $templateParams);
    }

    //TODO Optimize logic
    public function verifyBankAccountAction(Request $request)
    {
        $publicToken = $request->get('publicToken');
        $accountId = $request->get('accountId');

        try {
            $stripeBankAccountToken = $this->createBankAccountToken($publicToken, $accountId);
        } catch (ServiceException $e) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => $e->getMessage(),
                ]
            );
        }

        $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');
        $accountManager = $this->get('erp.payment.stripe.manager.account_manager');
        /** @var $user User */
        $user = $this->getUser();
        $stripeCustomer = $user->getStripeCustomer();

        $options = null;
        if ($user->hasRole(User::ROLE_TENANT)) {
            $manager = $user->getTenantProperty()->getUser();
            $managerStripeAccountId = $manager->getStripeAccount()->getAccountId();
            $options = ['stripe_account' => $managerStripeAccountId];
        }

        if (!$stripeCustomer) {
            $response = $customerManager->create(
                [
                    'email' => $user->getEmail(),
                    'source' => $stripeBankAccountToken,
                ],
                $options
            );

            if (!$response->isSuccess()) {
                return new JsonResponse(
                    [
                        'success' => false,
                        'error' => $response->getErrorMessage(),
                    ]
                );
            }
            /** @var Customer $customer */
            $customer = $response->getContent();

            $stripeCustomer = new StripeCustomer();
            $stripeCustomer->setCustomerId($customer['id'])
                ->setUser($user);

            $this->em->persist($stripeCustomer);
            // Force flush for saving Stripe customer
            $this->em->flush();
        } else {
            /** @var StripeCustomer $stripeCustomer */
            $response = $customerManager->retrieve($stripeCustomer->getCustomerId(), $options);

            if (!$response->isSuccess()) {
                return new JsonResponse(
                    [
                        'success' => false,
                        'error' => $response->getErrorMessage(),
                    ]
                );
            }
            /** @var Customer $customer */
            $customer = $response->getContent();
            $response = $customerManager->update($customer, ['source' => $stripeBankAccountToken], $options);

            if (!$response->isSuccess()) {
                return new JsonResponse(
                    [
                        'success' => false,
                        'error' => $response->getErrorMessage(),
                    ]
                );
            }
        }

        try {
            $stripeBankAccountToken = $this->createBankAccountToken($publicToken, $accountId);
        } catch (ServiceException $e) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => $e->getMessage(),
                ]
            );
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            if (!$user->hasStripeAccount()) {
                $response = $accountManager->create([
                    'country' => StripeAccount::DEFAULT_ACCOUNT_COUNTRY,
                    'type' => StripeAccount::DEFAULT_ACCOUNT_TYPE,
                    'external_account' => $stripeBankAccountToken,
                ]);

                if (!$response->isSuccess()) {
                    return new JsonResponse(
                        [
                            'success' => false,
                            'error' => $response->getErrorMessage(),
                        ]
                    );
                }
                /** @var Account $account */
                $account = $response->getContent();

                $stripeAccount = new StripeAccount();
                $stripeAccount->setUser($user)
                    ->setAccountId($account['id']);

                $this->em->persist($stripeAccount);
                // Force flush for saving Stripe account
                $this->em->flush();
            } else {
                $stripeAccount = $user->getStripeAccount();
                $response = $accountManager->retrieve($stripeAccount->getAccountId());

                if (!$response->isSuccess()) {
                    return new JsonResponse(
                        [
                            'success' => false,
                            'error' => $response->getErrorMessage(),
                        ]
                    );
                }
                /** @var Account $account */
                $account = $response->getContent();
                $response = $accountManager->update($account, ['external_account' => $stripeBankAccountToken]);

                if (!$response->isSuccess()) {
                    return new JsonResponse(
                        [
                            'success' => false,
                            'error' => $response->getErrorMessage(),
                        ]
                    );
                }
            }

            $form = $this->createForm(new AccountVerificationType());

            return $this->render('ErpStripeBundle:Widget:verification_ba.html.twig', [
                'form' => $form->createView(),
                'modalTitle' => 'Continue verification',
            ]);
        }

        return new JsonResponse(
            [
                'success' => true,
            ]
        );
    }

    public function payRentAction(Request $request)
    {
        $entity = new StripeRecurringPayment();

        $form = $this->createForm(new StripeRecurringPaymentType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $manager = $user->getTenantProperty()->getUser();
            $managerStripeAccount = $manager->getStripeAccount();
            $tenantStripeCustomer = $user->getStripeCustomer();

            if (!$managerStripeAccount || !$tenantStripeCustomer) {
                $this->addFlash(
                    'alert_error',
                    'An occurred error. Please, contact your system administrator.'
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }

            $propertyChecker = $this->get('erp_property.checker.property_checker');

            if (!$propertyChecker->isPayable($user, $entity)) {
                $this->addFlash(
                    'alert_error',
                    'You can\'t pay for this rent.'
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }

            $startPaymentAt = $entity->getStartPaymentAt();
            $entity
                ->setNextPaymentAt($startPaymentAt)
                ->setAccount($managerStripeAccount)
                ->setCustomer($tenantStripeCustomer);

            $em = $this->getDoctrine()->getManagerForClass(StripeRecurringPayment::class);
            $em->persist($entity);
            $em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        return $this->render('ErpPaymentBundle:Stripe\Widgets:rental-payment.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function verifyAccountAction(Request $request)
    {
        //TODO Need to verify account if I change BA?
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        $form = $this->createForm(new AccountVerificationType(), $stripeAccount,  ['validation_groups' => 'US']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $stripeAccount->setTosAcceptanceDate(new \DateTime())
                ->setTosAcceptanceIp($request->getClientIp());

            $apiManager = $this->get('erp_stripe.entity.api_manager');
            $arguments = [
                'id' => $stripeAccount->getAccountId(),
                'params' => $stripeAccount->toStripe(),
                'options' => null,

            ];
            $response = $apiManager->callStripeApi('\Stripe\Account', 'update', $arguments);

            if (!$response->isSuccess()) {
                return new JsonResponse([
                    'success' => false,
                    'error' => $response->getErrorMessage(),
                ]);
            }

            /** @var Account $content */
            $content = $response->getContent();
            if ($fieldsNeeded = $content->verification->fields_needed) {
                //TODO Handle Stripe required verification fields
                return new JsonResponse([
                    'success' => false,
                    'fields_needed' => $fieldsNeeded,
                ]);
            }

            $em = $this->getDoctrine()->getManagerForClass(StripeAccount::class);
            $em->persist($stripeAccount);
            $em->flush();

            if ($user->hasRole(User::ROLE_MANAGER)) {
                $url = $this->generateUrl('erp_property_unit_buy');
            } else {
                $url = $this->generateUrl('erp_user_profile_dashboard');
            }

            return new JsonResponse([
                'redirect' => $url,
            ]);
        }
        //TODO Prepare backend errors for frontend
        return $this->render('ErpStripeBundle:Widget:verification_ba.html.twig', [
            'form' => $form->createView(),
            'modalTitle' => 'Continue verification',
        ]);
    }

    private function createBankAccountToken($publicToken, $accountId)
    {
        $itemPlaidService = $this->get('erp.payment.plaid.service.item');
        $processorPlaidService = $this->get('erp.payment.plaid.service.processor');

        $response = $itemPlaidService->exchangePublicToken($publicToken);
        $result = json_decode($response['body'], true);

        if ($response['code'] < 200 || $response['code'] >= 300) {
            throw new ServiceException($result['display_message']);
        }

        $response = $processorPlaidService->createBankAccountToken($result['access_token'], $accountId);
        $result = json_decode($response['body'], true);

        if ($response['code'] < 200 || $response['code'] >= 300) {
            throw new ServiceException($result['display_message']);
        }

        return $result['stripe_bank_account_token'];
    }
}
