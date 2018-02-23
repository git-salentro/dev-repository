<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\LandlordFormType;
use Erp\StripeBundle\Entity\PaymentTypeInterface;
use Erp\UserBundle\Entity\BankAccountLandlordCharge;
use Erp\UserBundle\Entity\CreditCardLandlordCharge;
use Symfony\Component\HttpFoundation\Request;

class LandlordController extends BaseController
{
    //list of landlords inside Accounting menu
    public function indexAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $qb = $this->em->getRepository('ErpUserBundle:User')->findBy(['manager' => $this->getUser()]);
        $pagination = $this->get('knp_paginator')->paginate(
            $qb,
            $this->get('request')->query->get('page', 1) /*page number*/,
            10/*limit per page*/
        );

        return $this->render('ErpUserBundle:Landlords:index.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
            'modalTitle'=> 'Landlords management'
        ]);
    }


    public function createAction(Request $request)
    {

        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $landlord = new User();
        $form = $this->createForm(new LandlordFormType(), $landlord);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $landlord->setManager($user);
            $landlord->setUsername($landlord->getEmail());
            $landlord->setPlainPassword('12345');
            $landlord->setIsPrivatePaySimple(false);
            $this->em->persist($landlord);
            $this->em->flush();

            $this->addFlash('alert_ok', 'Landlord has been added successfully!');

            return $this->redirect($this->generateUrl('erp_user_accounting'));
        }

        return $this->render('ErpUserBundle:Landlords:create.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    public function chargeAction($token)
    {
//        /** @var User $user */
//        $user = $this->get('user_manager')->findUserByChargeToken($token);
//
//        if (!$user) {
//            return $this->createNotFoundException();
//        }

        return $this->render('ErpUserBundle:Landlords:charge.html.twig', [
            'token' => $token,
        ]);
    }

    public function confirmChargeAction(Request $request, $type, $token)
    {
//        /** @var User $user */
//        $user = $this->get('user_manager')->findUserByChargeToken($token);
//
//        if (!$user) {
//            return $this->createNotFoundException();
//        }

        /** @var PaymentTypeInterface $model */
        $model = $this->get('erp_stripe.registry.model_registry1')->getModel($type);
        $form = $this->get('erp_stripe.registry.form_registry1')->getForm($type);
        $form->setData($model);
        $form->handleRequest($request);

        $template =sprintf('ErpUserBundle:Landlords/Forms:%s.html.twig', $type);
        $params = [
            'token' => $token,
            'form' => $form->createView(),
        ];

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $stripeApiManager = $this->get('erp_stripe.entity.api_manager');
                $arguments = [
                    'options' => [
                        'amount' => $amount,
                        'currency' => StripeCustomer::DEFAULT_CURRENCY,
                        'source' => $model->getSourceToken(),
                    ],
                ];
                $response = $stripeApiManager->callStripeApi('\Stripe\Charge', 'create', $arguments);

                if (!$response->isSuccess()) {
                    $this->addFlash(
                        'alert_error',
                        $response->getErrorMessage()
                    );

                    return $this->render($template, $params);
                }

                $this->addFlash(
                    'alert_ok',
                    'Success'
                );
            } else {
                $this->addFlash(
                    'alert_error',
                    'Error'
                );
            }
        }

        return $this->render($template, $params);
    }
}
