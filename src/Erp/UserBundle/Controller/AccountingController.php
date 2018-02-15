<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Erp\CoreBundle\Controller\BaseController;

class AccountingController extends BaseController
{
    public function indexAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();


        //TODO: accounting page
        return $this->render('ErpUserBundle:Dashboard:accounting.html.twig', [
            'user'=>$user,
        ]);
    }
}
