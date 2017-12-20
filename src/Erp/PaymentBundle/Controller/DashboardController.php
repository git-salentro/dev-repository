<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\UserBundle\Entity\User;

class DashboardController extends BaseController
{
    public function dashboardAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('ErpPaymentBundle:Dashboard:dashboard.html.twig', [
            'user' => $user,
        ]);
    }
}