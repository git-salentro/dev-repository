<?php

namespace Erp\PropertyBundle\Controller;

use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Erp\PropertyBundle\Form\Type\ScheduledRentPaymentType;
use Erp\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ScheduledRentPaymentController extends Controller
{
    public function payRentAction(Request $request)
    {
        $entity = new ScheduledRentPayment();

        $form = $this->createForm(new ScheduledRentPaymentType(), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
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

                $startPaymentAt = $entity->getStartPaymentAt();
                $entity
                    ->setNextPaymentAt($startPaymentAt)
                    ->setAccount($managerStripeAccount)
                    ->setCustomer($tenantStripeCustomer);

                $em = $this->getDoctrine()->getManagerForClass(ScheduledRentPayment::class);
                $em->persist($entity);
                $em->flush();

                $this->addFlash(
                    'alert_ok',
                    'Success'
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            } else {
                $this->addFlash(
                    'alert_error',
                    $form->getErrors(true)[0]->getMessage()
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }
        }

        return $this->render('ErpPaymentBundle:Stripe\Widgets:rental-payment.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}