<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Form\Type\StripeCreditCardType;
use Erp\PaymentBundle\Plaid\Exception\ServiceException;
use Erp\PaymentBundle\Stripe\Model\CreditCard;
use Erp\UserBundle\Entity\User;
use Stripe\Account;
use Stripe\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends BaseController
{
    public function showPaymentDetailsAction()
    {
        /** @var User $user */
        $user = $this->getUser();
//        //TODO Add cache layer (APC or Doctrine)
//        $stripeUserManager = $this->get('erp.payment.stripe.manager.user_manager');
//        /** @var BankAccount $bankAccount */
//        $bankAccount = $stripeUserManager->getBankAccount($user);
//        /** @var Card $creditCard */
//        $creditCard = $stripeUserManager->getCreditCard($user);

        return $this->render(
            'ErpPaymentBundle:Stripe/Widgets:payment-details.html.twig',
            [
                'creditCard' => null,
                'bankAccount' => null,
                'customer' => $user->getPaySimpleCustomers()->first(),
            ]
        );
    }

    //TODO Optimize logic
    public function saveCreditCardAction(Request $request)
    {
        $form = $this->createForm(new StripeCreditCardType());
        /** @var CreditCard $model */
        $model = new CreditCard();

        $form->setData($model);
        $form->handleRequest($request);
        /** @var $user User */
        $user = $this->getUser();

        $template = 'ErpPaymentBundle:Stripe/Forms:cc.html.twig';
        $templateParams = [
            'type' => StripeCustomer::CREDIT_CARD,
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
            'customer' => $user->getStripeCustomers()->first(),
        ];

        if ($form->isValid()) {
            $landlord = $user->getTenantProperty()->getUser();
            $landlordStripeAccountId = $landlord->getStripeAccount()->getAccountId();

            $stripeToken = $model->getToken();
            $requestOptions = ['stripe_account' => $landlordStripeAccountId];

            $stripeCustomers = $user->getStripeCustomers();
            $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');

            if ($stripeCustomers->isEmpty()) {
                $response = $customerManager->create(
                    [
                        'email' => $user->getEmail(),
                        'source' => $stripeToken,
                    ],
                    $requestOptions
                );

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
                /** @var Customer $customer */
                $customer = $response->getContent();

                $stripeCustomer = new StripeCustomer();
                $stripeCustomer->setCustomerId($customer['id']);

                $user->addStripeCustomer($stripeCustomer);

                $this->em->persist($user);
                // Force flush for saving Stripe customer
                $this->em->flush();
            } else {
                /** @var StripeCustomer $stripeCustomer */
                $stripeCustomer = $stripeCustomers->last();
                $response = $customerManager->retrieve($stripeCustomer->getCustomerId(), $requestOptions);

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
                    $requestOptions
                );

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
            }

            return $this->redirectToRoute('erp_payment_unit_buy');
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
        $stripeCustomers = $user->getStripeCustomers();
        //TODO Why use ArrayCollection in PaySimple customers?
        if ($stripeCustomers->isEmpty()) {
            $response = $customerManager->create(
                [
                    'email' => $user->getEmail(),
                    'source' => $stripeBankAccountToken,
                ]
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
            $stripeCustomer->setCustomerId($customer['id']);

            $user->addStripeCustomer($stripeCustomer);

            $this->em->persist($user);
            // Force flush for saving Stripe customer
            $this->em->flush();
        } else {
            /** @var StripeCustomer $stripeCustomer */
            $stripeCustomer = $stripeCustomers->last();
            $response = $customerManager->retrieve($stripeCustomer->getCustomerId());

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
            $response = $customerManager->update($customer, ['source' => $stripeBankAccountToken]);

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

        return new JsonResponse(
            [
                'success' => true,
            ]
        );
    }

    public function payRentAction()
    {

    }

    public function showCashflowsAction()
    {
        return $this->render('ErpPaymentBundle:Stripe:cashflows.html.twig', [
            'cache_in' => [],
            'cache_out' => [],
        ]);
    }

    public function showInvoicesAction()
    {
        return $this->render('ErpPaymentBundle:Stripe:invoices.html.twig', []);
    }

    public function showTransactionsAction()
    {
        return $this->render('ErpPaymentBundle:Stripe:transactions.html.twig', []);
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
