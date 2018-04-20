<?php

namespace Erp\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\NotificationBundle\Form\Type\UserNotificationType;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\PropertyBundle\Entity\Property;
use Doctrine\ORM\QueryBuilder;
use Erp\UserBundle\Entity\User;
use Erp\NotificationBundle\Entity\Template;

class UserNotificationController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $form = $this->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $repo = $this->getDoctrine()->getManagerForClass(Property::class)->getRepository(Property::class);
            $properties = $repo->getPropertiesWithActiveTenant($user);

            return $this->render('ErpNotificationBundle:UserNotification:choose_properties.html.twig', [
                'properties' => $properties,
                'form' => $form->createView(),
            ]);
        }

        return $this->render('ErpNotificationBundle:UserNotification:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $repo = $this->getDoctrine()->getManagerForClass(UserNotification::class)->getRepository(UserNotification::class);
        $alerts = $repo->getAlertsByUser($user);

        return $this->render('ErpNotificationBundle:UserNotification:list.html.twig', [
            'alerts' => $alerts,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        $alerts = [];

        return $this->render('ErpNotificationBundle:UserNotification:history.html.twig', [
            'alerts' => $alerts,
        ]);
    }

    public function updateAction(Request $request)
    {

    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function choosePropertiesAction(Request $request)
    {
        $userNotification = new UserNotification();
        $form = $this->getForm($userNotification);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->redirectToRoute('erp_notification_user_notification_create');
        }

        /** @var User $user */
        $user = $this->getUser();

        $idx = $request->get('idx', []);
        $allElements = $request->get('all_elements', false);

        $em = $this->getDoctrine()->getManagerForClass(Property::class);
        $propertyRepository = $em->getRepository(Property::class);
        /** @var QueryBuilder $qb */
        $qb = $propertyRepository->getQueryBuilderByUser($user);

        if (count($idx) > 0) {
            $propertyRepository->addIdentifiersToQueryBuilder($qb, $idx);
        } elseif (!$allElements) {
            $query = null;
        }

        $em = $this->getDoctrine()->getManagerForClass(UserNotification::class);
        try {
            $i = 0;
            foreach ($qb->getQuery()->iterate() as $object) {
                /** @var Property $property */
                $property = $object[0];

                $userNotificationPrototype = clone $userNotification;
                $userNotificationPrototype->addProperty($property);

                $em->persist($userNotificationPrototype);

                if ((++$i % 20) == 0) {
                    $em->flush();
                    $em->clear();
                }
            }

            $em->flush();
            $em->clear();

            $this->addFlash(
                'alert_ok',
                'Success'
            );
        } catch (\PDOException $e) {
            throw $e;
        }

        return $this->redirectToRoute('erp_notification_user_notification_list');
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAlertAction($id)
    {
        /** @var User $user */
        $user = $this->getUser();

        $repo = $this->getDoctrine()->getManagerForClass(UserNotification::class)->getRepository(UserNotification::class);
        /** @var UserNotification $alert */
        $alert = $repo->getAlertByUserAndId($id, $user);

        return $this->render('ErpNotificationBundle:UserNotification:view_alert.html.twig', [
            'alert' => $alert,
        ]);
    }

    private function update(Request $request)
    {

    }

    private function getUserTemplates()
    {
        $repository = $this->getDoctrine()->getManagerForClass(Template::class)->getRepository(Template::class);
        return $repository->getTemplatesByUser($this->getUser());
    }

    private function getForm(UserNotification $userNotification = null)
    {
        $options = [
            'templates' => $this->getUserTemplates(),
        ];
        return $this->createForm(new UserNotificationType(), $userNotification, $options);
    }
}