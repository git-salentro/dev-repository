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
use Stripe\BankAccount;
use Stripe\Card;
use Erp\StripeBundle\Entity\Invoice;
use Erp\StripeBundle\Entity\Transaction;
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
        //TODO Add cache layer (APC or Doctrine)
        $stripeUserManager = $this->get('erp.payment.stripe.manager.user_manager');
        /** @var BankAccount $bankAccount */
        $bankAccount = $stripeUserManager->getBankAccount($user);
        /** @var Card $creditCard */
        $creditCard = $stripeUserManager->getCreditCard($user);

        return $this->render(
            'ErpPaymentBundle:Stripe/Widgets:payment-details.html.twig',
            [
                'creditCard' => $creditCard,
                'bankAccount' => $bankAccount,
            ]
        );
    }

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
            $landlord = $user->getTenantProperty()->getUser();
            $landlordStripeAccountId = $landlord->getStripeAccount()->getAccountId();

            $stripeToken = $model->getToken();
            $requestOptions = ['stripe_account' => $landlordStripeAccountId];

            $stripeCustomer = $user->getStripeCustomer();
            $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');

            if (!$stripeCustomer) {
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

                $user->setStripeCustomer($stripeCustomer);

                $this->em->persist($user);
                // Force flush for saving Stripe customer
                $this->em->flush();
            } else {
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
        //TODO Why use ArrayCollection in PaySimple customers?
        if (!$stripeCustomer) {
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
            $stripeCustomer->setCustomerId($customer['id'])
                ->setUser($user);

            $this->em->persist($stripeCustomer);
            // Force flush for saving Stripe customer
            $this->em->flush();
        } else {
            /** @var StripeCustomer $stripeCustomer */
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

        if ($user->hasRole(User::ROLE_LANDLORD)) {
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
            $landlord = $user->getTenantProperty()->getUser();
            $landlordStripeAccount = $landlord->getStripeAccount();
            $tenantStripeCustomer = $user->getStripeCustomer();

            if (!$landlordStripeAccount || !$tenantStripeCustomer) {
                $this->addFlash(
                    'alert_error',
                    ''
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }

            $propertyChecker = $this->get('erp_property_checker_property_checker');

            if (!$propertyChecker->isPayable($user, $entity)) {
                $this->addFlash(
                    'alert_error',
                    ''
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }

            $startPaymentAt = $entity->getStartPaymentAt();
            $entity
                ->setNextPaymentAt($startPaymentAt)
                ->setAccount($landlordStripeAccount)
                ->setCustomer($tenantStripeCustomer);

            $em = $this->getDoctrine()->getManagerForClass(StripeRecurringPayment::class);
            $em->persist($entity);
            $em->flush();

            $this->addFlash(
                'alert_ok',
                ''
            );

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        return $this->render('ErpPaymentBundle:Stripe\Widgets:rental-payment.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function showCashflowsAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();

        $items = [];
        if ($stripeAccount) {
            $transactionRepo = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
            $items = $transactionRepo->getGroupedTransactions($stripeAccount, $sixMonthsAgo, $now);
        }

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $months = array_keys($labels);
        $labels = array_values($labels);
        $casheIn = $this->getPreparedItems($items, $months);
        $casheOut =  $this->getPreparedItems($items, $months);

        return $this->render('ErpPaymentBundle:Stripe:cashflows.html.twig', [
            'cashe_in' => $casheIn,
            'cashe_out' => $casheOut,
            'labels' => $labels,
        ]);
    }

    public function showInvoicesAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();

        $items = [];
        if ($stripeAccount) {
            $invoicesRepo = $this->getDoctrine()->getManagerForClass(Invoice::class)->getRepository(Invoice::class);
            $items = $invoicesRepo->getGroupedInvoices($stripeAccount, $sixMonthsAgo, $now);
        }

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $months = array_keys($labels);
        $labels = array_values($labels);
        $invoices = $this->getPreparedItems($items, $months);

        return $this->render('ErpPaymentBundle:Stripe:invoices.html.twig', [
            'labels' => $labels,
            'invoices' => $invoices,
        ]);
    }

    public function showTransactionsAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();

        $items = [];
        if ($stripeAccount) {
            $transactionRepo = $this->getDoctrine()->getManagerForClass(Invoice::class)->getRepository(Transaction::class);
            $items = $transactionRepo->getGroupedTransactions($stripeAccount, $sixMonthsAgo, $now);
        }

        $labels = $this->getMonthsLabels($sixMonthsAgo, $now);
        $months = array_keys($labels);
        $labels = array_values($labels);
        $transactions = $this->getPreparedItems($items, $months);
        
        return $this->render('ErpPaymentBundle:Stripe:transactions.html.twig', [
            'transactions' => $transactions,
            'labels' => $labels,
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

    private function getMonthsLabels(\DateTime $dateFrom, \DateTime $dateTo)
    {
        $diff = $dateFrom->diff($dateTo);
        $count = ($diff->format('%y') * 12) + $diff->format('%m') +1;

        $labels = [];
        for ($i=1; $i<=$count; $i++) {
            $labels[$dateFrom->format('n')] = $dateFrom->format('F');
            $dateFrom->modify('+1 month');
        }

        return $labels;
    }

    private function getPreparedItems(array $items, array $months)
    {
        $results = [];
        $existingMonth = array_column($items, 'gMonth');
        foreach ($months as $month) {
            if (false !== $key = array_search($month, $existingMonth)) {
                $results[] = $items[$key]['gAmount'];
            } else {
                $results[] = 0;
            }
        }

        return $results;
    }
}
