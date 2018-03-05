<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\LateRentPaymentType;
use Erp\UserBundle\Entity\LateRentPayment;
use Erp\CoreBundle\Controller\BaseController;

class LateRentPaymentController extends BaseController
{
    /**
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(User $user, Request $request)
    {
        if (!$user->hasRole(User::ROLE_TENANT) && !$user->getLateRentPayments()) {
            throw $this->createAccessDeniedException();
        }

        $entity = new LateRentPayment();
        $entity->setUser($user);

        return $this->update($entity, $request);
    }

    /**
     * @param LateRentPayment $lateRentPayment
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateAction(LateRentPayment $lateRentPayment, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->hasTenant($lateRentPayment->getUser())) {
            return $this->createNotFoundException();
        }

        return $this->update($lateRentPayment, $request);
    }

    /**
     * @param LateRentPayment $lateRentPayment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeAction($id)
    {
        /** @var User $user */
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManagerForClass(LateRentPayment::class);
        $repository = $em->getRepository(LateRentPayment::class);
        $lateRentPayment = $repository->find($id);

        if ($user->hasTenant($lateRentPayment->getUser())) {
            return $this->createNotFoundException();
        }

        $lateRentPayment->setPaid(true);
        $em->persist($lateRentPayment);
        $em->flush();

        $this->addFlash(
            'alert_ok',
            'Success'
        );

        return $this->redirectToRoute('erp_user_dashboard_dashboard');
    }

    public function removeUserAction(User $user, Request $request)
    {
        /** @var User $user */
        $currentUser = $this->getUser();

        if ($currentUser->hasTenant($user)) {
            return $this->createNotFoundException();
        }

        if ($request->getMethod() === 'DELETE') {
            $user->clearLateRentPayments();

            $em = $this->getDoctrine()->getManagerForClass(User::class);
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirectToRoute('erp_user_dashboard_dashboard');
        }

        return $this->render('ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig', [
            'actionUrl' => $this->generateUrl('erp_user_late_rent_payment_remove_user', ['id' => $user->getId()]),
        ]);
    }

    /**
     * @param LateRentPayment $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function update(LateRentPayment $entity, Request $request)
    {
        $form = $this->createForm(new LateRentPaymentType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManagerForClass(LateRentPayment::class);
            $em->persist($entity);
            $em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
        }

        return $this->render('ErpUserBundle:LateRentPayment:form.html.twig', [
            'modalTitle' => 'Set Late Rent Payment Settings',
            'form' => $form->createView(),
        ]);
    }
}
