<?php

namespace Erp\UserBundle\Controller;

use Erp\PropertyBundle\Entity\RentPayment;
use Erp\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RentPaymentController extends Controller
{
    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManagerForClass(RentPayment::class);
        $repository = $em->getRepository(RentPayment::class);
        /** @var RentPayment $rentPayment */
        //TODO Check user in condition
        $rentPayment = $repository->find($id);

        if (!$rentPayment) {
            return $this->createNotFoundException();
        }

        $rentPayment->setBalance(0);
        $tenant = $rentPayment->getUser();
        $lateRentPaymentSettings = $tenant->getLateRentPaymentSettings();

        $em->remove($lateRentPaymentSettings);
        $em->persist($rentPayment);
        $em->flush();

        $this->addFlash(
            'alert_ok',
            'Success'
        );

        return $this->redirectToRoute('erp_user_dashboard_dashboard');
    }
}