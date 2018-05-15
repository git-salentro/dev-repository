<?php

namespace Erp\NotificationBundle\Controller;

use DoctrineExtensions\Query\Mysql\Date;
use Erp\CoreBundle\Controller\BaseController;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Symfony\Component\HttpFoundation\Request;
use Erp\NotificationBundle\Entity\EvictionData;
use Erp\NotificationBundle\Entity\Template;
use Erp\NotificationBundle\Form\Type\TemplateType;
use Erp\UserBundle\Entity\User;
use Erp\NotificationBundle\Form\EvictionDataType;
use Erp\PropertyBundle\Entity\Property;

/**
 * EvictionData controller.
 *
 */
class EvictionDataController extends BaseController
{
    /**
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ErpNotificationBundle:EvictionData')->findBy(['user'=>['user'=>$user]]);

        return $this->render(
            'ErpNotificationBundle:EvictionData:index.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     */
    public function SendEvictionAction(Request $request)
    {
        $user = $this->getUser();
        $entity = new EvictionData();

        $options = [
            'templates' => $this->getUserTemplates(),
            'action' =>  $this->generateUrl('erp_notification_user_eviction_create'),
            'method' => 'POST',
        ];

        $form = $this->createForm(new EvictionDataType(), $entity, $options);

        $form->handleRequest($request);

        if ($request->getMethod() === 'POST') {
            if ($form->isValid()) {

                $rawData = $request->get('erp_notificationbundle_evictiondata', []);
                $checkDate = '-'.$rawData['days'];

                $repo = $this->getDoctrine()->getManagerForClass(Property::class)->getRepository(Property::class);
                $properties = $repo->GetRentLatePropertiesListForEviction($user,$checkDate);
                return $this->render(
                    'ErpNotificationBundle:EvictionData:show.html.twig',
                    [
                        'entity' => $entity,
                        'properties' => $properties,
                        'form' => $form->createView(),
                    ]
                );
            } else {
                $this->addFlash('alert_error', 'Please fill all required data...');
                return $this->redirectToRoute('erp_notification_user_eviction_create');
            }
        }

        return $this->render(
            'ErpNotificationBundle:EvictionData:new.html.twig',
            [
                'entity' => $entity,
                'form'   => $form->createView(),
            ]
        );
    }

    /**
     */
    public function newAction(Request $request)
    {

        $entity = new EvictionData();
        $form = $this->getForm($entity);
        $form->handleRequest($request);
        if (!$form->isValid()) {
            return $this->redirectToRoute('erp_notification_user_eviction_create');
        }

        try {
                $idx = $request->get('idx', []);
                if (count($idx) > 0) {
                    $eviction_data = $request->get('erp_notificationbundle_evictiondata', []);
                    $dayUntilDue = $eviction_data['days'];
                    $templateId = $eviction_data['template'];

                    /** template data */
                    $tem = $this->getDoctrine()->getManagerForClass(Template::class);
                    $templateRepository = $tem->getRepository(Template::class);
                    $template = $templateRepository->find(['id' => $templateId]);

                    /** property data */
                    $em = $this->getDoctrine()->getManagerForClass(Property::class);
                    $propertyRepository = $em->getRepository(Property::class);

                    $user = $this->getUser();
                    /** @var QueryBuilder $qb */
                    $qb = $propertyRepository->getQueryBuilderByUser($user);

                    $propertyRepository->addIdentifiersToQueryBuilder($qb, $idx);
                    $i = 0;

                    /** Set default null value for sent or not sent*/
                    $notSent = $sent = '';

                    /** get all selected property data for send eviction notice and need to check some condtions */

                    foreach ($qb->getQuery()->iterate() as $object) {
                        $property = $object[0];

                        $condition_1 = new \DateTime();

                        $evictionEm = $this->getDoctrine()->getManager();

                        $chk = $evictionEm->getRepository('ErpNotificationBundle:EvictionData')->findBy(['properties'=>$property,'user'=>$user],['id'=>'DESC'],['limit'=>1]);

                        /** check for didn't have any send eviction for particular property with those tenant
                         * and after that store in eviction data and send out to admin for further process
                         **/
                        if(!$chk)
                        {
                            $entity->setDays($dayUntilDue);
                            $entity->setTemplate($template);
                            $entity->setUser($user);
                            $entity->addProperty($property);
                            $entity->setDescription('Under-Process');
                            $entity->prePersist();

                            $em = $this->getDoctrine()->getManager();
                            $em->persist($entity);
                            $em->flush();

                            /** assign tenants for sent eviction*/
                            $tenant = $property->getTenantUser();
                            $sent.= $tenant->getFirstName().' '.$tenant->getLastName().', ';
                        }
                        else
                        {
                            /** if already sent eviction for particular property with those tenant but need to check 1 day over or not **
                             ** and after that store in eviction data and send out to admin for further process **
                             **/
                            $chkDate = $chk[0];

                            $recordDate = $chkDate->getCreatedAt();
                            $dayDiff = $recordDate->diff($condition_1);

                            if($dayDiff->d == 1)
                            {
                                $entity->setDays($dayUntilDue);
                                $entity->setTemplate($template);
                                $entity->setUser($user);
                                $entity->addProperty($property);
                                $entity->setDescription('Under-Process');
                                $entity->prePersist();

                                $em = $this->getDoctrine()->getManager();
                                $em->persist($entity);
                                $em->flush();

                                /** assign tenants for sent eviction*/
                                $tenant = $property->getTenantUser();
                                $sent.= $tenant->getFirstName().' '.$tenant->getLastName().', ';
                            }
                            else
                                {
                                    /** assign tenants for didn't sent eviction*/
                                    $tenant = $property->getTenantUser();
                                    $notSent.= $tenant->getFirstName().' '.$tenant->getLastName().', ';
                                }

                        }
                    }

                    /** Flash msg for sent successfully eviction*/
                    if($sent){
                        $this->addFlash('alert_ok', 'Eviction Action successfully perform and Eviction Send to these tenants '.$sent);
                    }

                    /** Flash msg for didn't sent eviction*/
                    if($notSent) {
                        $this->addFlash('alert_error', 'Today Already sent eviction to these tenants '.$notSent);
                    }
                    return $this->redirectToRoute('erp_notification_user_eviction');
                } else {

                    $this->addFlash('alert_error', 'Please select at least one property and try again...');

                    return $this->redirectToRoute('erp_notification_user_eviction_create');
                }

        }
        catch (\PDOException $e) {
        throw $e;
        }
        return $this->redirectToRoute('erp_notification_user_eviction');
    }

    private function getUserTemplates()
    {
        $repository = $this->getDoctrine()->getManagerForClass(Template::class)->getRepository(Template::class);
        return $repository->getTemplatesByUser($this->getUser());
    }
    private function getForm(EvictionData $entity = null)
    {
        $options = [
            'templates' => $this->getUserTemplates(),
        ];
        return $this->createForm(new EvictionDataType(), $entity, $options);
    }
}
