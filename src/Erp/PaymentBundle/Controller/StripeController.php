<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Stripe\Model\PaymentTypeInterface;
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

        if (!$user->hasAccessToPaymentPage()) {
            throw new AccessDeniedException;
        }

        $amount = $this->get('erp.core.fee.service')->getFees();
        $amount = $amount ? $amount->getErentPay() : 0;

        $formTypesRegistry = $this->get('erp.payment.stripe.registry.form_registry');
        $modelRegistry = $this->get('erp.payment.stripe.registry.model_registry');

        $form = $formTypesRegistry->getForm($type);
        $model = $modelRegistry->getModel($type);

        $form->setData($model);
        $form->handleRequest($request);

        $template = 'ErpPaymentBundle:Stripe:form.html.twig';
        $templateParams = [
            'type' => $type,
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
            'amount' => $amount,
            'customer' => $user->getStripeCustomers()->first(),
            'isLandlord' => $user->hasRole(User::ROLE_LANDLORD),
            'checkPaymentAmount' => $this->get('erp.core.fee.service')->getCheckPaymentFee(),
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
            /** @var PaymentTypeInterface $model */
            $model = $form->getData();
            $paymentTypeProvider = $this->get('erp.payment.stripe.provider.payment_type_provider');
            $response = $paymentTypeProvider->createPayment($customer, $type, $model->toArray());

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            $content = $response->getContent();
            $paymentTypeProvider->updateIdField($stripeCustomer, $type, $content['id']);

            $this->em->persist($stripeCustomer);
            $this->em->flush();

            if ($content instanceof BankAccount) {
                $response = $customerManager->verifyBankAccount($content);

                if (!$response->isSuccess()) {
                    $this->addFlash('alert_error', $response->getErrorMessage());
                }
            }

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

            return $this->redirectToRoute('erp_payment_unit_buy');
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

    public function payRentAction()
    {

    }
}