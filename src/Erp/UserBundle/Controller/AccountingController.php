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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AccountingController extends BaseController
{
    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function indexAction(Request $request)
    {
        return $this->render('ErpUserBundle:Accounting:index.html.twig');
    }


    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
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

        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];
        $keyword = $data['keyword'];

        $pagination = [];
        if ($stripeAccount) {
            $stripeAccountId = $stripeAccount ? $stripeAccount->getId() : null;
            $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
            $query = $repository->getTransactionsSearchQuery($stripeAccountId, null, $dateFrom, $dateTo, null, $keyword);

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
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]
            );
        }
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
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

        $dateFrom = $data['dateFrom'];
        $dateTo = $data['dateTo'];
        $keyword = $data['keyword'];

        $pagination = [];
        if ($stripeAccount) {
            $stripeAccountId = $stripeAccount ? $stripeAccount->getId() : null;
            $repository = $this->getDoctrine()->getManagerForClass(Transaction::class)->getRepository(Transaction::class);
            $query = $repository->getTransactionsSearchQuery($stripeAccountId, null, $dateFrom, $dateTo, null, $keyword);

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
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]
            );
        }
    }

    private function getFilterParameters(Request $request)
    {
        return $request->query->get(AbstractFilterType::NAME, []);
    }
}
