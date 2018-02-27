<?php

namespace Erp\UserBundle\Controller;

use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Erp\CoreBundle\Controller\BaseController;
use Erp\StripeBundle\Form\Type\TransactionFilterType;

class AccountingController extends BaseController
{
    public function indexAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        //TODO: accounting page
        return $this->render('ErpUserBundle:Accounting:index.html.twig', [
            'user' => $user,
        ]);
    }

    public function showAccountingLedgerAction(Request $request)
    {
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();

        $form = $this->createForm(new TransactionFilterType($tokenStorage));
        $form->handleRequest($request);

        $data = $form->getData();
        $stripeCustomer = $data['landlord'];
        $stripeAccount = $user->getStripeAccount();
        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];

        $pagination = [];
        if ($stripeAccount) {
            $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
            $query = $repository->getTransactionsQuery($stripeAccount, $stripeCustomer, $dateFrom, $dateTo);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1)
            );
        }

        return $this->render('ErpUserBundle:Accounting:accounting_ledger.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }
}
