<?php

namespace Erp\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\NotificationBundle\Form\Type\UserNotificationType;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\PropertyBundle\Entity\Property;
use Doctrine\ORM\QueryBuilder;
use Erp\UserBundle\Entity\User;

class UserNotificationController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new UserNotificationType());
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

    public function updateAction(Request $request)
    {

    }

    public function choosePropertiesAction(Request $request)
    {
        $userNotification = new UserNotification();
        $form = $this->createForm(new UserNotificationType(), $userNotification);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render('ErpNotificationBundle:UserNotification:create.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($data = json_decode($request->get('data'), true)) {
            $idx = $data['idx'];
            $allElements = $data['all_elements'];
        } else {
            $idx = $request->get('idx', []);
            $allElements = $request->get('all_elements', false);
        }

        $propertyRepository = $this->getDoctrine()->getRepository(Property::class);
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
                $userNotificationPrototype->setProperty($property);

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

        $this->addFlash(
            'alert_ok',
            'Success'
        );

        return $this->redirectToRoute('erp_notification_user_notification_crate');
    }

    public function viewAlertAction($id)
    {

    }

    private function update(Request $request)
    {

    }
}
