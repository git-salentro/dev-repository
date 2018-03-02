<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\UserBundle\Entity\Charge;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\ChargeFormType;
use Erp\UserBundle\Form\Type\LandlordFormType;
use Erp\StripeBundle\Entity\PaymentTypeInterface;
use Erp\StripeBundle\Helper\ApiHelper;
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

        //manager charge landlord Step 1 (select) in twig
        return $this->render('ErpUserBundle:Landlords:index.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
            'modalTitle' => 'Landlords management'
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
            $landlord->setManager($user)
                ->setUsername($landlord->getEmail())
                ->setPlainPassword('12345')
                ->setIsPrivatePaySimple(false);

            $this->em->persist($landlord);
            $this->em->flush();

            $this->addFlash('alert_ok', 'Landlord has been added successfully!');

            return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
        }

        return $this->render('ErpUserBundle:Landlords:create.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    public function chargeAction(Request $request)
    {
        //TODO: fetch landlords ids (multiple selection)

        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $landlordId = $request->get('landlordId');
        $landlord = $this->em->getRepository('ErpUserBundle:User')->findOneBy(['id' => $landlordId]);

        if ($landlord instanceof User) {
            //Second step

            /** @var $user \Erp\UserBundle\Entity\User */
            $charge = new Charge();
            $form = $this->createForm(new ChargeFormType(), $charge);
            $form->handleRequest($request);

            /** @var $manager \Erp\UserBundle\Entity\User */
            $manager = $landlord->getManager();

            if ($manager->getId() == $user->getId() && $form->isValid()) {
                //Third (Final) step

                $charge->setManager($user);
                $charge->setLandlord($landlord);
                $this->em->persist($charge);
                $this->em->flush();

                $this->get('erp_user.mailer.processor')->sendChargeEmail($charge);

                return $this->render('ErpUserBundle:Landlords:chargeComplete.html.twig', [
                    'charge' => $charge,
                    'modalTitle' => 'Sent',
                    'user' => $user,
                    'landlord' => $landlord
                ]);

            }
            return $this->render('ErpUserBundle:Landlords:charge.html.twig', [
                'charge' => $charge,
                'user' => $user,
                'landlord' => $landlord,
                'form' => $form->createView()
            ]);

        } else {
            //back to landlords list to select
            $this->addFlash('alert_error', 'Choose any landlord to charge');
            return $this->forward('ErpUserBundle:Landlord:index');

        }

    }

    /**
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function chooseChargeTypeAction($token)
    {
        $repository = $this->getDoctrine()->getManagerForClass(Charge::class)->getRepository(Charge::class);
        /** @var Charge $charge */
        $charge = $repository->find($token);

        if (!$charge) {
            return $this->createNotFoundException();
        }

        return $this->render('ErpUserBundle:Landlords:choose_charge_type.html.twig', [
            'token' => $token,
            'charge' => $charge,
        ]);
    }

    /**
     * @param Request $request
     * @param $type
     * @param $token
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function confirmChargeAction(Request $request, $type, $token)
    {
        $em = $this->getDoctrine()->getManagerForClass(Charge::class);
        $repository = $em->getRepository(Charge::class);
        /** @var Charge $charge */
        $charge = $repository->find($token);

        if (!$charge) {
            return $this->createNotFoundException();
        }

        if ($charge->isPaid()) {
            return $this->createNotFoundException();
        }

        /** @var PaymentTypeInterface $model */
        $model = $this->get('erp_stripe.registry.model_registry1')->getModel($type);
        $form = $this->get('erp_stripe.registry.form_registry1')->getForm($type);
        $form->setData($model);
        $form->handleRequest($request);

        $template = sprintf('ErpUserBundle:Landlords/Forms:%s.html.twig', $type);
        $params = [
            'token' => $token,
            'form' => $form->createView(),
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $manager */
            $manager = $charge->getManager();
            $stripeApiManager = $this->get('erp_stripe.entity.api_manager');
            $arguments = [
                'params' => [
                    'amount' => ApiHelper::convertAmountToStripeFormat($charge->getAmount()),
                    'currency' => StripeCustomer::DEFAULT_CURRENCY,
                    'source' => $model->getSourceToken(),
                ],
                'options' => [
                    'stripe_account' => $manager->getStripeAccount()->getAccountId(),
                ]
            ];
            $response = $stripeApiManager->callStripeApi('\Stripe\Charge', 'create', $arguments);

            if (!$response->isSuccess()) {
                $this->addFlash(
                    'alert_error',
                    $response->getErrorMessage()
                );

                return $this->render($template, $params);
            }

            $charge->setStatus(Charge::STATUS_PAID);
            $em->persist($charge);
            $em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirect('/');
        }

        return $this->render($template, $params);
    }
}
