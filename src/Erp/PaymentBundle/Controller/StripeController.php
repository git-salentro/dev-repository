<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Stripe\Model\PaymentTypeInterface;
use Erp\StripeBundle\Entity\Invoice;
use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Stripe\Account;
use Stripe\BankAccount;
use Stripe\Card;
use Stripe\Customer;
use Stripe\Token;
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
                'customer' => $user->getPaySimpleCustomers()->first(),
            ]
        );
    }

    public function choosePaymentMethodAction($type)
    {
        /** @var $user User */
        $user = $this->getUser();

        $formTypesRegistry = $this->get('erp.payment.stripe.registry.form_registry');
        $form = $formTypesRegistry->getForm($type);

        return $this->render('ErpPaymentBundle:Stripe:form.html.twig', [
            'type' => $type,
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
            'customer' => $user->getStripeCustomers()->last(),
        ]);
    }

    //TODO Optimize logic. Use consumer
    public function saveCreditCardAction(Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();

        $formTypesRegistry = $this->get('erp.payment.stripe.registry.form_registry');
        $modelRegistry = $this->get('erp.payment.stripe.registry.model_registry');

        $form = $formTypesRegistry->getForm(StripeCustomer::CREDIT_CARD);
        /** @var PaymentTypeInterface $model */
        $model = $modelRegistry->getModel(StripeCustomer::CREDIT_CARD);

        $form->setData($model);
        $form->handleRequest($request);

        $template = 'ErpPaymentBundle:Stripe:form.html.twig';
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
            $requestOptions = ['stripe_account' => $landlordStripeAccountId];

            $tokenManager = $this->get('erp.payment.stripe.manager.token_manager');
            $response = $tokenManager->create(
                [
                    'card' => $model->toArray()
                ],
                $requestOptions
            );

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
            /** @var Token $token */
            $token = $response->getContent();

            $stripeCustomers = $user->getStripeCustomers();
            $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');

            if ($stripeCustomers->isEmpty()) {
                $response = $customerManager->create(
                    [
                        'email' => $user->getEmail(),
                        'source' => $token['id'],
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
                $customer = $customerManager->retrieve($stripeCustomer->getCustomerId(), $requestOptions);
                $response = $customerManager->update(
                    $customer,
                    [
                        'source' => $token['id']
                    ],
                    $requestOptions
                );

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
            }
        }

        return $this->render($template, $templateParams);
    }

    //TODO Optimize logic. Use consumer?
    public function verifyBankAccountAction(Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();

        $itemPlaidService = $this->get('erp.payment.plaid.service.item');
        $processorPlaidService = $this->get('erp.payment.plaid.service.processor');

        $result = $itemPlaidService->exchangePublicToken($request->get('publicToken'));

        if (400 == $result['code']) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => 'An occurred error.',
                ]
            );
        }

        $result = json_decode($result['body']);
        $result = $processorPlaidService->bankAccountTokenCreate($result->access_token, $request->get('accountId'));

        if (400 == $result['code']) {
            return new JsonResponse(
                [
                    'success' => false,
                    'error' => 'An occurred error.',
                ]
            );
        }

        $result = json_decode($result['body']);
        $stripeBankAccountToken = $result['stripe_bank_account_token'];

        $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');
        $accountManager = $this->get('erp.payment.stripe.manager.account_manager');

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

        return new JsonResponse([
            'success' => true,
        ]);
    }

    public function payRentAction()
    {

    }

    public function showCashflowsAction()
    {
        /** @var User $user */
        $user = $this->getUser();

        $now = new \DateTime();
        $sixMonthsAgo = (new \DateTime())->modify('-5 month');
        $stripeAccount = $user->getStripeAccount();

        $transactionsRepo = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
        $items = $transactionsRepo->getGroupedTransactions($stripeAccount, $sixMonthsAgo, $now);

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

        $invoicesRepo = $this->getDoctrine()->getManagerForClass(Invoice::class)->getRepository(Invoice::class);
        $items = $invoicesRepo->getGroupedInvoices($stripeAccount, $sixMonthsAgo, $now);

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
        return $this->render('ErpPaymentBundle:Stripe:transactions.html.twig', []);
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
