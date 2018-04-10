<?php

namespace Erp\NotificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\NotificationBundle\Entity\Template;
use Erp\NotificationBundle\Form\Type\TemplateType;

class TemplateController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        return $this->render('ErpNotificationBundle:Template:list.html.twig');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $entity = new Template();
        return $this->update($entity, $request);
    }

    /**
     * @param Template $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Template $entity, Request $request)
    {
        return $this->update($entity, $request);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
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

    /**
     * @param Template $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function update(Template $entity, Request $request)
    {
        $form = $this->createForm(new TemplateType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManagerForClass(Template::class);
            $em->persist($entity);
            $em->flush();
        }

        return $this->render('ErpNotificationBundle:Template:create.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }
}