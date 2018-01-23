<?php

namespace Erp\StripeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;

class TransactionController extends Controller
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

        //TODO Do more flexible. Create filter model, form
        $interval = $request->query->get('filter[interval]', null, true);

        $dateFrom = \DateTimeImmutable::createFromFormat('Y-n', $interval)->modify('first day of this month')->setTime(0, 0, 0);
        $dateTo = $dateFrom->modify('+1 month');

        $dateFrom = (new \DateTime())->setTimestamp($dateFrom->getTimestamp());
        $dateTo = (new \DateTime())->setTimestamp($dateTo->getTimestamp());

        $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
        $query = $repository->getTransactions($stripeAccount, $dateFrom, $dateTo);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)
        );

        return $this->render('ErpStripeBundle:Transaction:index.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }
}
