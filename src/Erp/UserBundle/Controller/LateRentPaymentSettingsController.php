<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Erp\UserBundle\Form\Type\LateRentPaymentSettingsType;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\LateRentPaymentSettings;
use Erp\CoreBundle\Controller\BaseController;

class LateRentPaymentSettingsController extends BaseController
{
    /**
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(User $user, Request $request)
    {
        if ($this->isAllowed($user)) {
            throw $this->createAccessDeniedException();
        }

        $entity = new LateRentPaymentSettings();
        $entity->setUser($user);

        return $this->update($entity, $request);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(User $user, Request $request)
    {
        if ($this->isAllowed($user)) {
            throw $this->createAccessDeniedException();
        }

        $entity = $user->getLateRentPaymentSettings();

        return $this->update($entity, $request);
    }

    /**
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     */
    public function allowRentPaymentAction(User $user, Request $request)
    {
        if ($this->isAllowed($user)) {
            throw $this->createAccessDeniedException();
        }

        if (!$entity = $user->getLateRentPaymentSettings()) {
            $entity = new LateRentPaymentSettings();
            $entity->setUser($user);
        }

        $form = $this->createForm(new LateRentPaymentSettingsType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManagerForClass(LateRentPaymentSettings::class);
            $em->persist($entity);
            $em->flush();

            return new JsonResponse([
                'success' => true,
            ]);
        }

        return new JsonResponse([
            'success' => false,
            'error' => 'An occurred an error.',
        ]);
    }

    /**
     * @param LateRentPaymentSettings $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function update(LateRentPaymentSettings $entity, Request $request)
    {
        $form = $this->createForm(new LateRentPaymentSettingsType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManagerForClass(LateRentPaymentSettings::class);
            $em->persist($entity);
            $em->flush();

            $this->addFlash(
                'alert_ok',
                'Success'
            );

            return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
        }

        return $this->render('ErpUserBundle:LateRentPaymentSettings:form.html.twig', [
            'modalTitle' => 'Set Late Rent Payment Settings',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function isAllowed(User $user)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $currentUser->hasTenant($user) && $user->hasRole(User::ROLE_TENANT);
    }
}
