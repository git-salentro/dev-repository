<?php

namespace Erp\UserBundle\Controller;

use Erp\StripeBundle\Entity\Transaction;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Erp\CoreBundle\Controller\BaseController;
use Erp\StripeBundle\Form\Type\TransactionFilterType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Erp\StripeBundle\Form\Type\AbstractFilterType;

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

    // TODO Remove that. Crate one method with showAccountingLedgerAction
    public function listAccountingLedgerAction(Request $request, $_format = 'html')
    {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();

        $requestStack = $this->get('request_stack');
        $masterRequest = $requestStack->getMasterRequest();

        $form = $this->createForm(new TransactionFilterType($tokenStorage));
        $form->handleRequest($masterRequest);

        $data = $form->getData();
        $stripeAccount = $user->getStripeAccount();
        /** @var User $landlord */
        $landlord = $data['landlord']; //receiver
        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];

        $pagination = [];
        if ($stripeAccount) {
            $stripeCustomer = $landlord ? $landlord->getStripeCustomer() : null;
            $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
            $query = $repository->getTransactionsBothDirectionsQuery($stripeAccount, $stripeCustomer, $dateFrom, $dateTo);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1)
            );
        }

        $template = sprintf('ErpUserBundle:Accounting:accounting_ledger_list.html.twig', $_format);
        $parameters = [
            'user' => $user,
            'form' => $form->createView(),
            'pagination' => $pagination
        ];

        if ($_format == 'html') {
            $urlParameters = array_merge(
                ['_format' => 'pdf'],
                ['filter' => $this->getFilterParameters($masterRequest)]
            );
            $parameters['pdf_link'] = $this->generateUrl('erp_user_accounting_show_accounting_ledger', $urlParameters);

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

    public function showAccountingLedgerAction(Request $request, $_format = 'html')
    {
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->get('security.token_storage');
        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();

        $requestStack = $this->get('request_stack');
        $masterRequest = $requestStack->getMasterRequest();

        $form = $this->createForm(new TransactionFilterType($tokenStorage));
        $form->handleRequest($masterRequest);

        $data = $form->getData();
        $stripeAccount = $user->getStripeAccount();
        /** @var User $landlord */
        $landlord = $data['landlord']; //receiver
        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];

        $pagination = [];
        if ($stripeAccount) {
            $stripeCustomer = $landlord ? $landlord->getStripeCustomer() : null;
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
            $urlParameters = array_merge(
                ['_format' => 'pdf'],
                ['filter' => $this->getFilterParameters($masterRequest)]
            );
            $parameters['pdf_link'] = $this->generateUrl('erp_user_accounting_show_accounting_ledger', $urlParameters);

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

    private function getFilterParameters(Request $request)
    {
        return $request->query->get(AbstractFilterType::NAME, []);
    }
}
