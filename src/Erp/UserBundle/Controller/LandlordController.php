<?php

namespace Erp\UserBundle\Controller;

use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\LandlordFormType;
use Symfony\Component\HttpFoundation\Request;
use Erp\CoreBundle\Controller\BaseController;

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

        return $this->render('ErpUserBundle:Landlords:index.html.twig', [
            'user' => $user,
            'pagination' => $pagination
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
            $landlord->setManager($user);
            $landlord->setUsername($landlord->getEmail());
            $landlord->setPlainPassword('12345');
            $landlord->setIsPrivatePaySimple(false);
            $this->em->persist($landlord);
            $this->em->flush();
        }

        return $this->render('ErpUserBundle:Landlords:create.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

}
