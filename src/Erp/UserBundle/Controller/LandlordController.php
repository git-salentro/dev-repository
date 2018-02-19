<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Erp\CoreBundle\Controller\BaseController;

class LandlordController extends BaseController
{
    //list of landlords inside Accounting menu
    public function indexAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        return $this->render('ErpUserBundle:Landlords:index.html.twig', [
            'user' => $user,
        ]);
    }

    //add new landlord and assign to current manager
    public function createAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        return $this->render('ErpUserBundle:Landlords:create.html.twig', [
            'user' => $user,
        ]);
    }

}
