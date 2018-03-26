<?php

namespace Erp\SignatureBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\Entity\Document;
use Erp\PropertyBundle\Form\Type\ESignFormType;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\UserDocument;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SignatureController
 *
 * @package Erp\SignatureBundle\Controller
 */
class SignatureController extends BaseController
{
    /**
     * Send document at esign
     *
     * @param Request $request
     * @param         $documentId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function eSignAction(Request $request, $documentId)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        /** @var Document $document */
        $document = $this->em->getRepository('ErpCoreBundle:Document')->find($documentId);
        if (!$document) {
            throw $this->createNotFoundException('Document not found');
        }

        $form = $this->createESignForm($document);

        $renderOptions = [
            'eSignFee' => $this->get('erp.core.fee.service')->getESignFee(),
            'modalTitle' => 'Enter your email address',
            'isTenant' => $user->hasRole(User::ROLE_TENANT),
        ];

        if ($user = $this->getUser()) {
            $renderOptions = array_merge($renderOptions, ['user' => $user]);
        }

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // Payment
                $amount = $this->get('erp.core.fee.service')->getESignFee();

                $payer = ($user->hasRole(User::ROLE_TENANT))
                    ? $user->getTenantProperty()->getUser()
                    : $user;

                $customer = $payer->getPaySimpleCustomers()->first();
                $accountId = $customer->getPrimaryType() === PaySimpleManagerInterface::CREDIT_CARD
                    ? $customer->getCcId()
                    : $customer->getBaId();
                $paymentModel = new RecurringPaymentModel();
                $paymentModel->setAmount($amount)
                    ->setCustomer($customer)
                    ->setStartDate(new \DateTime())
                    ->setAccountId($accountId);

                $paymentResponse = $this->get('erp.users.user.service')->makeOnePayment($paymentModel);

                if (!$paymentResponse['status']) {
                    $this->get('erp.payment.paysimple_service')->sendPaymentEmail($customer);
                    if ($user->hasRole(User::ROLE_TENANT)) {
                        $msg =
                            $this->get('erp.users.user.service')->getPaySimpleErrorByCode('charge_esign_tenant_error');
                    } else {
                        $msg = $this->get('erp.users.user.service')->getPaySimpleErrorByCode('error');
                    }

                    $renderOptions = array_merge(
                        $renderOptions,
                        [
                            'modalTitle' => 'Error',
                            'msg' => $msg,
                        ]
                    );
                } else {
                    $email = $form->get('email')->getData();
                    $response =
                        $this->get('erp.signature.docusign.service')->createEnvelopeFromDocument($document, $email);

                    $this->get('erp.logger')->add('docusign', json_encode($response));
                    if (isset($response->status) && $response->status === 'sent') {
                        $msg =
                            $this->get('erp.users.user.service')->getPaySimpleErrorByCode('charge_esign_tenant_ok')
                            . '<br/>Document to be signed is sent to your email, please check ' . $email . '.';

                        $renderOptions = array_merge(
                            $renderOptions,
                            [
                                'modalTitle' => 'Success',
                                'msg' => $msg,
                            ]
                        );
                    } else {
                        $msg = 'Please try again later or contact your administrator';
                        $renderOptions = array_merge(
                            $renderOptions,
                            [
                                'modalTitle' => 'Error',
                                'msg' => isset($response->message) ? $response->message : $msg
                            ]
                        );
                    }
                }
            }
        }

        $renderOptions = array_merge($renderOptions, ['form' => $form->createView()]);

        return $this->render('ErpPropertyBundle:Form:esign-form.html.twig', $renderOptions);
    }

    public function editEnvelopAction($userDocumentId)
    {
        $em = $this->getDoctrine()->getManagerForClass(UserDocument::class);
        $repository = $em->getRepository(UserDocument::class);
        /** @var UserDocument $document */
        $userDocument = $repository->find($userDocumentId);

        if (!$userDocument || !$userDocument->getDocument()) {
            throw $this->createNotFoundException('Document not found');
        }

        /** @var User $user */
        $user = $this->getUser();

        $sender = $user;
        $recipient = $userDocument->getToUser();

        try {
            $docusignService = $this->get('erp.signature.docusign.service');

            if (!$userDocument->getEnvelopId()) {
                $response = $docusignService->createEnvelopeFromDocumentNew($userDocument->getDocument(), $sender, $recipient);

                $userDocument->setEnvelopId($response->envelopeId);

                $em->persist($userDocument);
                $em->flush();
            }

            $url = $docusignService->createCorrectLink($userDocument->getEnvelopId(), $recipient);

            return new RedirectResponse($url);
        } catch (\Exception $e) {
            return new Response($e->getMessage());
        }
    }

    /**
     * Create ESign form
     *
     * @param Document $document
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createESignForm(Document $document)
    {
        $type = new ESignFormType();
        $action = $this->generateUrl('erp_esign_form', ['documentId' => $document->getId()]);

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm($type, null, $formOptions);

        return $form;
    }
}
