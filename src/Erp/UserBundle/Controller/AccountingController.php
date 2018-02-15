<?php

namespace Erp\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Erp\CoreBundle\Controller\BaseController;

class AccountingController extends BaseController
{
    public function indexAction(Request $request)
    {
        //TODO: accounting page
        return $this->render('ErpUserBundle:Dashboard:accounting.html.twig', [
            'pagination' => [],
        ]);
    }
}
