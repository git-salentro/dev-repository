<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Entity\StripeSubscription;
use Stripe\Subscription;
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
        //manager charge landlord Step 1 (select) in twig
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $items = $this->getDoctrine()->getManagerForClass(User::class)->getRepository(User::class)->findBy(['manager' => $user]);

        return $this->render('ErpUserBundle:Landlords:index.html.twig', [
            'user' => $user,
            'items' => $items,
            'modalTitle' => 'Charge clients'
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

            return $this->redirect($this->generateUrl('erp_user_accounting_index'));
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
        $chargeEm = $this->getDoctrine()->getManagerForClass(Charge::class);
        /** @var Charge $charge */
        $charge = $chargeEm->getRepository(Charge::class)->find($token);

        if (!$charge || $charge->isPaid()) {
            return $this->createNotFoundException();
        }

        /** @var PaymentTypeInterface $model */
        $model = $this->get('erp_stripe.registry.model_registry')->getModel($type);
        $form = $this->get('erp_stripe.registry.form_registry')->getForm($type);
        $form->setData($model);
        $form->handleRequest($request);

        $template = sprintf('ErpUserBundle:Landlords/Forms:%s.html.twig', $type);
        $params = [
            'token' => $token,
            'form' => $form->createView(),
            'charge' => $charge,
        ];

        if (!$form->isSubmitted()) {
            return $this->render($template, $params);
        }

        if (!$form->isValid()) {
            return $this->render($template, $params);
        }

        /** @var User $landlord */
        $landlord = $charge->getLandlord();
        /** @var User $manager */
        $manager = $charge->getManager();
        $landlordStripeCustomer = $landlord->getStripeCustomer();
        $managerStripeAccount = $manager->getStripeAccount();

        $stripeApiManager = $this->get('erp_stripe.entity.api_manager');

        if (!$managerStripeAccount) {
            $this->addFlash(
                'alert_error',
                'Manager can not accept payments.'
            );
            return $this->render($template, $params);
        }

        $sourceToken = $model->getSourceToken();
        $stripeAccountId = $managerStripeAccount->getAccountId();

        if (!$landlordStripeCustomer) {
            $arguments = [
                'params' => [
                    'email' => $landlord->getEmail(),
                    'source' => $sourceToken,
                ],
                'options' => [
                    'stripe_account' => $stripeAccountId,
                ],
            ];
            $response = $stripeApiManager->callStripeApi('\Stripe\Customer', 'create', $arguments);

            if (!$response->isSuccess()) {
                $this->addFlash(
                    'alert_error',
                    $response->getErrorMessage()
                );
                return $this->render($template, $params);
            }

            /** @var \Stripe\Customer $externalStripeCustomer */
            $externalStripeCustomer = $response->getContent();

            $landlordStripeCustomer = new StripeCustomer();
            $landlordStripeCustomer->setCustomerId($externalStripeCustomer->id)
                ->setUser($landlord);

            $landlord->setStripeCustomer($landlordStripeCustomer);

            $userEm = $this->getDoctrine()->getManagerForClass(User::class);
            $userEm->persist($landlord);
            $userEm->flush();
        }

        $amount = $charge->getAmount();

        if ($charge->isRecurring()) {
            //TODO: add possibility for many subscriptions
            $landlordStripeSubscription = $landlordStripeCustomer->getStripeSubscription();

            if (!$landlordStripeSubscription) {
                $arguments = [
                    'id' => StripeSubscription::MONTHLY_PLAN_ID,
                    'options' => [
                        'stripe_account' => $stripeAccountId,
                    ]
                ];
                $response = $stripeApiManager->callStripeApi('\Stripe\Plan', 'retrieve', $arguments);

                if (!$response->isSuccess()) {
                    $arguments = [
                        'params' => [
                            'amount' => 1,
                            'interval' => 'month',
                            "currency" => 'usd',
                            'name' => StripeSubscription::MONTHLY_PLAN_ID,
                            'id' => StripeSubscription::MONTHLY_PLAN_ID,
                        ],
                        'options' => [
                            'stripe_account' => $stripeAccountId,
                        ]
                    ];
                    $response = $stripeApiManager->callStripeApi('\Stripe\Plan', 'create', $arguments);

                    if (!$response->isSuccess()) {
                        $this->addFlash(
                            'alert_error',
                            $response->getErrorMessage()
                        );
                        return $this->render($template, $params);
                    }
                }

                $arguments = [
                    'params' => [
                        'customer' => $landlordStripeCustomer->getCustomerId(),
                        'items' => [
                            [
                                'plan' => StripeSubscription::MONTHLY_PLAN_ID,
                                'quantity' => ApiHelper::convertAmountToStripeFormat($amount),
                            ],
                        ],
                    ],
                    'options' => [
                        'stripe_account' => $stripeAccountId,
                    ]
                ];
                $response = $stripeApiManager->callStripeApi('\Stripe\Subscription', 'create', $arguments);

                if (!$response->isSuccess()) {
                    $this->addFlash(
                        'alert_error',
                        $response->getErrorMessage()
                    );
                    return $this->render($template, $params);
                }
                /** @var Subscription $subscription */
                $subscription = $response->getContent();

                $stripeSubscription = new StripeSubscription();
                $stripeSubscription->setSubscriptionId($subscription['id'])
                    ->setStripeCustomer($landlordStripeCustomer);

                $landlordStripeCustomer->setStripeSubscription($stripeSubscription);

                $stripeCustomerEm = $this->getDoctrine()->getManagerForClass(Charge::class);
                $stripeCustomerEm->persist($landlordStripeCustomer);
            } else {
                //TODO ERP-191
                $arguments = [
                    'id' => $landlordStripeSubscription->getSubscriptionId(),
                    'params' => [
                        'quantity' => $amount,
                    ],
                    'options' => null,
                ];
                $response = $stripeApiManager->callStripeApi('\Stripe\Subscription', 'update', $arguments);

                if (!$response->isSuccess()) {
                    $this->addFlash(
                        'alert_error',
                        $response->getErrorMessage()
                    );
                    return $this->render($template, $params);
                }
            }
        } else {
            $arguments = [
                'params' => [
                    'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                    'customer' => $landlordStripeCustomer->getCustomerId(),
                    'currency' => StripeCustomer::DEFAULT_CURRENCY,
                    'metadata' => [
                        'account' => $stripeAccountId,
                        'internalType' => 'rent_payment'
                    ],
                ],
                'options' => [
                    'stripe_account' => $stripeAccountId,
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
        }

        $charge->setStatus(Charge::STATUS_PAID);

        $chargeEm->persist($charge);
        $chargeEm->flush();

        $this->addFlash(
            'alert_ok',
            'Success'
        );

        return $this->redirect('/');
    }
}
