<?php

namespace Erp\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\NotificationBundle\Entity\Template;
use Erp\NotificationBundle\Form\Type\TemplateType;

class TemplateController extends Controller
{
    public function listAction()
    {
        return $this->render('ErpNotificationBundle:Template:list.html.twig');
    }

    public function createAction(Request $request)
    {
        $entity = new Template();
        return $this->update($entity, $request);
    }

    public function updateAction(Template $entity, Request $request)
    {
        return $this->update($entity, $request);
    }

    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManagerForClass(Template::class);
        $repository = $em->getRepository(Template::class);

        /** @var Template $template */
        $template = $repository->find($id);

        $em->remove($template);
        $em->flush();

        return $this->redirectToRoute('erp_notification_template_list');
    }

    private function update(Template $entity, Request $request)
    {
        $form = $this->createForm(new TemplateType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManagerForClass(Template::class);
            $em->persist($entity);
            $em->flush();
        }

        return $this->render('ErpNotificationBundle:Template:create.html.twig');
    }
}