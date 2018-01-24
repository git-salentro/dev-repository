<?php

namespace Erp\StripeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Form\Type\CashflowFilterType;
use Erp\UserBundle\Entity\User;
use Erp\StripeBundle\Guesser\TransactionTypeGuesser;

class CashflowController extends Controller
{
    public function indexAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        if (!$stripeAccount) {
            return $this->render('ErpStripeBundle:Transaction:index.html.twig',[
                'error' => 'Please, verify you bank account.'
            ]);
        }

        $form = $this->createForm(new CashflowFilterType());
        $form->handleRequest($request);

        $data = $form->getData();
        $transactionTypeGuesser = new TransactionTypeGuesser();
        $guessedType = $transactionTypeGuesser->guess($data['type']);
        $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
        $query = $repository->getTransactions($stripeAccount, $data['dateFrom'], $data['dateTo'], $guessedType);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1));
        //TODO Remove user form all controller. Set user in template
        return $this->render('ErpStripeBundle:Cashflow:index.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
            'type' => $data['type'],
            'date_from' => $data['dateFrom'],
        ]);
    }
}
