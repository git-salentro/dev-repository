<?php

namespace Erp\UserBundle\Controller;

use Erp\UserBundle\Entity\Charge;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\ChargeFormType;
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

        //manager charge landlord Step 1 (select) in twig
        return $this->render('ErpUserBundle:Landlords:index.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
            'modalTitle' => 'Landlords management'
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

            $this->addFlash('alert_ok', 'Landlord has been added successfully!');

            return $this->redirect($this->generateUrl('erp_user_accounting'));
        }

        return $this->render('ErpUserBundle:Landlords:create.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }


    public function chargeAction(Request $request)
    {
        //manager charge landlord Step 2 (choose payment type and amount)
        //TODO: fetch landlords ids (multiple selection)

        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $landlordId = $request->attributes->get('landlordId');
        $landlord = $this->em->getRepository('ErpUserBundle:User')->findOneBy(['id' => $landlordId]);
        if ($landlord instanceof User) {
            /** @var $user \Erp\UserBundle\Entity\User */
            $charge = new Charge();
            $form = $this->createForm(new ChargeFormType(), $charge);
            $form->handleRequest($request);
            if ($form->isValid()) {

                $charge->setManager($user);
                $charge->setLandlord($landlord);
                $this->em->persist($charge);
                $this->em->flush();

                //manager charge landlord Step 2 - complete

                return $this->render('ErpUserBundle:Landlords:chargeComplete.html.twig', [
                    'charge' => $charge,
                    'modalTitle' => 'Sent'
                ]);
            }
        } else {
            //back to landlords list to select
            return $this->redirect($this->generateUrl('erp_user_landlords'));
        }

        return $this->render('ErpUserBundle:Landlords:chargeComplete.html.twig', [
            'form' => $form,
            'modalTitle' => 'Charge complete'
        ]);




    }


}
