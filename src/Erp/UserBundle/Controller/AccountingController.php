<?php

namespace Erp\UserBundle\Controller;

use Erp\StripeBundle\Entity\Transaction;
use Erp\StripeBundle\Repository\TransactionRepository;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Controller\BaseController;
use Erp\StripeBundle\Form\Type\TransactionFilterType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

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

    public function showAccountingLedgerAction(Request $request, $_format = 'html')
    {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();

        $requestStack = $this->get('request_stack');

        $form = $this->createForm(new TransactionFilterType($tokenStorage));
        $form->handleRequest($requestStack->getMasterRequest());

        $data = $form->getData();
        $stripeCustomer = $data['landlord']; //receiver
        $stripeAccount = $user->getStripeAccount();
        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];

        $pagination = [];
        if ($stripeAccount) {
            /** @var TransactionRepository $repository */
            $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
            $query = $repository->getTransactionsBothDirectionsQuery($stripeAccount, $stripeCustomer, $dateFrom, $dateTo);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1)
            );
        }

        $template = sprintf('ErpUserBundle:Accounting:accounting_ledger.%s.twig', $_format);
        $parameters = [
            'user' => $user,
            'form' => $form->createView(),
            'pagination' => $pagination,
        ];

        if ($_format == 'html') {
            return $this->render($template, $parameters);
        } elseif ($_format == 'pdf') {
            $fileName = sprintf('accounting_ledger_%s.pdf', (new \DateTime())->format('d_m_Y'));
            $html = $this->renderView($template, $parameters);
            $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

            return new Response(
                $pdf,
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition'  => 'attachment; filename="' . $fileName . '"',
                ]
            );
        }
    }
}
