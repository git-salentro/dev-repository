<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Stripe\Model\BankAccount as BankAccountModel;
use Erp\PaymentBundle\Stripe\Model\CreditCard;
use Erp\UserBundle\Entity\User;
use Stripe\BankAccount;
use Stripe\Card;
use Stripe\Customer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Erp\PaymentBundle\Form\Type\UnitType;

class StripeController extends BaseController
{
    public function createPaymentAccountAction($type, Request $request)
    {
        /** @var $user User */
        $user = $this->getUser();
        //TODO Create voter
        $access = $this->checkAccessToPaymentPage($user);

        if ($access) {
            throw $access;
        }

        $amount = $this->get('erp.core.fee.service')->getFees();
        $amount = $amount ? $amount->getErentPay() : 0;

        $formTypesRegistry = $this->get('erp.payment.registry.stripe_form_registry');
        $modelRegistry = $this->get('erp.payment.registry.stripe_models');

        $form = $formTypesRegistry->getForm($type);
        $model = $modelRegistry->getModel($type);

        $form->setData($model);
        $form->handleRequest($request);

        $unitForm = $this->createForm(new UnitType());

        $template = 'ErpPaymentBundle:Stripe:form.html.twig';
        $templateParams = [
            'type' => $type,
            'user' => $user,
            'form' => $form->createView(),
            'unitForm' => $unitForm->createView(),
            'errors' => null,
            'amount' => $amount,
            'customer' => $user->getStripeCustomers()->first(),
            'isLandlord' => $user->hasRole(User::ROLE_LANDLORD),
            'checkPaymentAmount' => $this->get('erp.core.fee.service')->getCheckPaymentFee(),
            'success' => false,
        ];

        if ($form->isValid() && $form->isSubmitted()) {
            $stripeCustomers = $user->getStripeCustomers();
            $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');

            if ($stripeCustomers->isEmpty()) {
                $response = $customerManager->create([
                    'email' => $user->getEmail(),
                ]);

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
                /** @var Customer $customer */
                $customer = $response->getContent();
                $content = $customer->__toArray();

                $stripeCustomer = new StripeCustomer();
                $stripeCustomer->setCustomerId($content['id']);

                $user->addStripeCustomer($stripeCustomer);

                $this->em->persist($user);
                $this->em->flush();
            } else {
                /** @var StripeCustomer $stripeCustomer */
                $stripeCustomer = $stripeCustomers->first();
                $response = $customerManager->retrieve($stripeCustomer->getCustomerId());
                /** @var Customer $customer */
                $customer = $response->getContent();

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
            }

            //TODO Optimize logic
            if ($type == 'cc') {
                /** @var CreditCard $creditCard */
                $creditCard = $form->getData();
                $response = $customerManager->createCard($customer, $creditCard->__toArray());
            } else {
                /** @var BankAccountModel $creditCard */
                $bankAccount = $form->getData();
                $response = $customerManager->createBankAccount($customer, $bankAccount->__toArray());
            }

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            if ($type == 'cc') {
                $stripeCustomer->setCreditCardId($response->getContent()['id']);
            } else {
                $stripeCustomer->setBankAccountId($response->getContent()['id']);
            }

            $this->em->persist($stripeCustomer);
            $this->em->flush();

//            $modelMethodSet = 'set' . strtoupper($type) . 'Id';
//            $modelMethodGet = 'get' . strtoupper($type).'Id';
//
//            $isFirst = !$stripeCustomer->{$modelMethodGet}() && !$stripeCustomer->getPrimaryType();
//            $isType = $stripeCustomer->{$modelMethodGet}() && $stripeCustomer->getPrimaryType() === $type;
//            $isHasRecurring = !$stripeCustomer->getPsRecurringPayments()->first() && $user->hasRole(User::ROLE_LANDLORD);
//
//            if ($isType || $isHasRecurring || $isFirst) {
//                $stripeCustomer->setPrimaryType($type);
//            }
//
//            $content = $response->getContent();
//            $stripeCustomer->{$modelMethodSet}($content['Id']);
//
//            $this->em->persist($stripeCustomer);
//            $this->em->flush();

//            $msg = 'Bank & Cards Information has been successfully changed';
//            if (!$user->isActive()) {
//                $msg = 'Please wait for account activation by Administrator';
//            }
//
//            $this->addFlash('alert_ok', $msg);
//
//            return $this->redirectToRoute('erp_payment_ps_create_account', ['type' => $type]);
            $templateParams['success'] = true;
            return $this->render($template, $templateParams);
        }

        return $this->render($template, $templateParams);
    }

    public function bankCardsAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        //TODO Add cache layer (APC or Doctrine)
        if (!$user->hasRole(User::ROLE_TENANT) && !$user->hasRole(User::ROLE_LANDLORD)) {
            throw $this->createNotFoundException();
        }

        $stripeUserManager = $this->get('erp.payment.stripe.manager.user_manager');
        /** @var BankAccount $bankAccount */
        $bankAccount = $stripeUserManager->getBankAccount($user);
        /** @var Card $creditCard */
        $creditCard = $stripeUserManager->getCreditCard($user);

        return $this->render(
            'ErpPaymentBundle:Stripe/Widgets:bank-cards-info.html.twig',
            [
                'creditCard' => $creditCard,
                'bankAccount' => $bankAccount,
                'customer' => $user->getPaySimpleCustomers()->first(),
            ]
        );
    }

    /**
     * Check if user has access to payment page
     *
     * @param User $user
     *
     * @return null|AccessDeniedException
     */
    private function checkAccessToPaymentPage(User $user = null)
    {
        $result = null;
        if (!$user || (!$user->hasRole(User::ROLE_LANDLORD) && !$user->hasRole(User::ROLE_TENANT))) {
            $result = new AccessDeniedException;
        }

        return $result;
    }
}