<?php

namespace Erp\PropertyBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\Entity\City;
use Erp\CoreBundle\Entity\Document;
use Erp\CoreBundle\Entity\FeeOption;
use Erp\CoreBundle\Entity\Image;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerInterface;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RecurringPaymentModel;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertyRepostRequest;
use Erp\PropertyBundle\Form\Type\EditDocumentPropertyFormType;
use Erp\PropertyBundle\Form\Type\EditImagePropertyFormType;
use Erp\PropertyBundle\Form\Type\EditPropertyFormType;
use Erp\PropertyBundle\Form\Type\PropertyImportFormType;
use Erp\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ListingController extends BaseController
{
    /**
     * Available properties page
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $properties = $user->getProperties()->filter(function ($property) {
            if ($property->getStatus() == Property::STATUS_DELETED) {
                $return = false;
            } else {
                $return = true;
            }

            return $return;
        });

        $currentPage = $request->query->getInt('page', 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $properties,
            $currentPage,
            Property::LIMIT_USER_LISTINGS
        );

        $prevPage = ceil($pagination->getTotalItemCount() / Property::LIMIT_USER_LISTINGS);
        if ($prevPage && $currentPage > $prevPage) {
            return $this->redirectToRoute('erp_property_listings_all', ['page' => $prevPage]);
        }

        $form = $this->createPropertyImportFormType();

        if ($request->getMethod() === 'POST') {
            $session = $this->get('session');
            if ($session->has('properties')) {
                $session->remove('properties');
            }
            $form->handleRequest($request);
            if ($form->isValid()) {
                $result =
                    $this->get('erp.property.service')
                        ->getEstimationImportFromCsv($user, $form->get('file')->getData());

                $session->set('properties', $result['properties']);

                return $this->redirectToRoute('erp_property_listings_import');
            }
        }

        $propertyFee = $this->get('erp.core.fee.service')->getPropertyFee();

        return $this->render(
            'ErpPropertyBundle:Listings:index.html.twig',
            [
                'user'                 => $user,
                'pagination'           => $pagination,
                'form'                 => $form->createView(),
                'propertyFee'          => $propertyFee,
                'propertyStatusRented' => Property::STATUS_RENTED,
            ]
        );
    }

    /**
     * Import Properties
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function importAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $session = $this->get('session');

        if (!$session->has('properties')) {
            throw $this->createNotFoundException();
        }

        $properties = $session->get('properties');

        $amount = $this->get('erp.core.fee.service')->getPropertyFee() * count($properties);

        if ($request->getMethod() == 'POST') {
            $customer = $user->getPaySimpleCustomers()->first();
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
                $this->addFlash('alert_error', $this->get('erp.users.user.service')->getPaySimpleErrorByCode('error'));
            } else {
                /** @var Property $property */
                foreach ($properties as $property) {
                    $property->setCreatedDate();
                    $property->setUpdatedDate(new \DateTime());
                    $this->em->merge($property);
                }

                $this->em->flush();
                $session->remove('properties');
                $this->addFlash(
                    'alert_ok',
                    $this->get('erp.users.user.service')->getPaySimpleErrorByCode('import_properties_ok')
                );
            }

            return $this->redirectToRoute('erp_property_listings_all');
        }

        $propertyFee = $this->get('erp.core.fee.service')->getPropertyFee();

        return $this->render(
            'ErpPropertyBundle:Listings:import.html.twig',
            [
                'user' => $user,
                'properties' => $properties,
                'propertyFee' => $propertyFee,
                'amount' => $amount,
            ]
        );
    }

    /**
     * Create|Edit Property page
     *
     * @param Request $request
     * @param int|null $propertyId
     *
     * @return RedirectResponse|Response|NotFoundHttpException
     */
    public function editAction(Request $request, $propertyId)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if ($propertyId) {
            $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

            if (!$property) {
                throw $this->createNotFoundException();
            }
        } else {
            if ($user->getPropertyCounter() > 0 || $user->getIsPropertyCounterFree()) {
                $property = new Property();
                $property->setUser($user);
            } else {
                throw $this->createNotFoundException();
            }
        }

        $pageNumber = $request->get('page', 1);

        $form = $this->createEditPropertyForm($property);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                /** @var $property \Erp\PropertyBundle\Entity\Property */
                $property = $form->getData();

                if (!$user->getIsPropertyCounterFree() && !$user->isReadOnlyUser() && !$propertyId) {
                    $this->em->persist($user->setPropertyCounter($user->getPropertyCounter() - 1));
                }

                $this->em->persist($property);
                $this->em->flush();

                $this->addFlash('alert_ok', 'Property was saved successfully.');

                return $this->redirectToRoute('erp_property_listings_all');
            }
        }

        return $this->render(
            'ErpPropertyBundle:Listings:edit.html.twig',
            [
                'user'     => $user,
                'form'     => $form->createView(),
                'property' => $property,
                'page'     => $pageNumber,
            ]
        );
    }

    /**
     * Charge for added property
     *
     * @param Request $request
     *
     * @return Response
     */
    public function addChargeAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        if ($user->isReadOnlyUser()) {
            throw $this->createNotFoundException();
        }

        $amount = $this->get('erp.core.fee.service')->getPropertyFee();

        if ($request->getMethod() == 'POST') {
            $customer = $user->getPaySimpleCustomers()->first();
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
                $this->addFlash('alert_error', $this->get('erp.users.user.service')->getPaySimpleErrorByCode('error'));
                $redirect = $this->redirectToRoute('erp_property_listings_all');
            } else {
                $this->addFlash(
                    'alert_ok',
                    $this->get('erp.users.user.service')->getPaySimpleErrorByCode('create_property_ok')
                );
                $this->em->persist($user->setPropertyCounter($user->getPropertyCounter() + 1));
                $this->em->flush();
                $redirect = $this->redirectToRoute('erp_property_listings_add');
            }

            return $redirect;
        }

        return $this->render(
            'ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig',
            [
                'actionUrl' => $this->generateUrl('erp_property_listings_add_charge_popup'),
                'actionBtn' => 'Yes',
                'cancelBtn' => 'No',
                'askMsg' => 'You will be charged $' . $amount . ' for this feature. Do you want to proceed?',
            ]
        );
    }

    /**
     * Manage listing documents action
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return Response
     */
    public function editDocumentsAction(Request $request, $propertyId)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        $pageNumber = $request->get('page', 1);

        $form = $this->createEditDocumentPropertyForm($property, $pageNumber);

        if ($request->getMethod() == 'POST') {
            $preValidate = $this->preValidateFiles(
                $request,
                $property,
                $form->getName(),
                ['documents', 'file']
            );

            $request = $preValidate['request'];
            $errors = $preValidate['errors'];

            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->em->persist($property);
                $this->em->flush();
            } else {
                $errors = true;
            }

            if ($errors) {
                $text = str_replace(
                    ['{maxSize}', '{sizeIn}'],
                    [Document::$maxSize / 1024 / 1024, Document::SIZE_IN_MB],
                    Document::$commonMessage
                );

                $this->addFlash('alert_error', $text);
            }

            return $this->redirectToRoute(
                'erp_property_listings_edit_documents',
                ['propertyId' => $property->getId(), 'page' => $pageNumber]
            );
        }

        return $this->render(
            'ErpPropertyBundle:Listings:edit-documents.html.twig',
            ['user' => $user, 'form' => $form->createView(), 'property' => $property, 'page' => $pageNumber]
        );
    }

    /**
     * Manage listing documents action
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return Response
     */
    public function editImagesAction(Request $request, $propertyId)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        /** @var Property $property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->getPropertyByUser($user, $propertyId);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        $pageNumber = $request->get('page', 1);

        $form = $this->createEditImagePropertyForm($property, $pageNumber);

        if ($request->getMethod() == 'POST') {
            $preValidate = $this->preValidateFiles(
                $request,
                $property,
                $form->getName(),
                ['images', 'image']
            );

            $request = $preValidate['request'];
            $errors = $preValidate['errors'];

            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->em->persist($property);
                $this->em->flush();
            } else {
                $errors = true;
            }

            if ($errors) {
                $text = str_replace(
                    ['{maxSize}', '{sizeIn}'],
                    [Image::$maxSize / 1024 / 1024, Document::SIZE_IN_MB],
                    Image::$commonMessage
                );

                $this->addFlash('alert_error', $text);
            }

            return $this->redirectToRoute(
                'erp_property_listings_edit_images',
                ['propertyId' => $property->getId(), 'page' => $pageNumber]
            );
        }

        return $this->render(
            'ErpPropertyBundle:Listings:edit-images.html.twig',
            ['user' => $user, 'form' => $form->createView(), 'property' => $property, 'page' => $pageNumber]
        );
    }

    /**
     * Pre validate files
     *
     * @param Request  $request
     * @param Property $property
     * @param string   $formName
     * @param array    $fields
     *
     * @return array
     */
    protected function preValidateFiles(Request $request, Property $property, $formName, $fields)
    {
        $files = $request->files->get($formName);
        $data = $request->request->get($formName);

        $errors = [];

        if (isset($files[$fields[0]]) && isset($data[$fields[0]])) {
            $files = $files[$fields[0]];

            $data = $data[$fields[0]];

            foreach ($data as $key => $item) {
                switch ($fields[0]) {
                    /* Documents */
                    case 'documents':
                        $file = new Document();
                        $file->setFile($files[$key][$fields[1]]);
                        $file->setOriginalName($item['originalName']);
                        $property->addDocument($file);
                        break;
                    /* Images */
                    case 'images':
                        $file = new Image();
                        $file->setImage($files[$key][$fields[1]]);
                        $property->addImage($file);
                        break;
                    /* Default */
                    default:
                        throw $this->createNotFoundException();
                }

                /** @var $errors \Symfony\Component\Validator\ConstraintViolationListInterface */
                $errorsValidate = $this->get('validator')->validate($file, null, ['EditProperty']);
                if ($errorsValidate->count()) {
                    unset($data[$key]);
                    unset($files[$key]);

                    $errors[] = $errorsValidate->get(0)->getMessage();
                }
            }

            $request->files->set($formName, [$fields[0] => $files]);
            $request->request->set($formName, [$fields[0] => $data]);
        }

        return ['request' => $request, 'errors' => $errors];
    }

    /**
     * Delete property
     *
     * @param Request $request
     * @param         $propertyId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function deleteAction(Request $request, $propertyId)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        if ($user->isReadOnlyUser()) {
            throw $this->createNotFoundException();
        }

        /** @var Property $property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')
            ->findOneBy(['id' => $propertyId, 'user' => $user]);

        if (!$property) {
            throw $this->createNotFoundException();
        }

        if ($property->getTenantUser() instanceof User || $property->getInvitedUsers()[0]) {
            $response = $this->render(
                'ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig',
                [
                    'askMsg'        => 'Please, remove tenant',
                    'hideActionBtn' => true,
                    'cancelBtn'     => 'OK',
                ]
            );
        } elseif ($request->getMethod() === 'DELETE') {
            if (!$user->isReadOnlyUser()) {
                $this->em->persist($user->setPropertyCounter($user->getPropertyCounter() + 1));
            }

            $deletedDate = new \DateTime('now');
            $property->setName(
                $property->getName() . '[DELETED-' . $deletedDate->format('m/d/Y H:i:s') . ']'
            );
            $property->setStatus(Property::STATUS_DELETED);
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        } else {
            $response = $this->render(
                'ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig',
                [
                    'askMsg'       => 'Are you sure you want to delete this property?',
                    'actionUrl'    => $this->generateUrl(
                        'erp_property_listings_delete',
                        ['propertyId' => $property->getId()]
                    ),
                    'actionMethod' => 'DELETE'
                ]
            );
        }

        return $response;
    }

    /**
     * Remove tenant from property
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return JsonResponse
     */
    public function deleteTenantAction(Request $request, $propertyId)
    {
        if ($this->getUser()->isReadOnlyUser()) {
            throw $this->createNotFoundException();
        }
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->find($propertyId);
        $tenant = $property->getTenantUser();

        $askMsg = 'Are you sure you want to delete this tenant?';
        if (!$property || $property->getUser()->getId() !== $this->getUser()->getId()) {
            $askMsg = '404 Not Found';
        }

        if ($request->getMethod() === 'DELETE') {
            $userService = $this->get('erp.users.user.service');
            $userService->deactivateUser($tenant);
            $userService->setStatusUnreadMessages($tenant);

            $property->setTenantUser(null)->setStatus(Property::STATUS_DRAFT);
            $this->em->persist($property);

            $invitedUser = $property->getInvitedUsers()->first();
            if ($invitedUser) {
                $this->em->remove($invitedUser);
            }

            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig',
            [
                'askMsg'    => $askMsg,
                'actionUrl' => $this->generateUrl('erp_property_listings_delete_tenant', ['propertyId' => $propertyId])
            ]
        );
    }

    /**
     * Remove tenant with status pending
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function deleteInvitedTenantAction(Request $request, $propertyId)
    {
        /** @var User $user */
        $user = $this->getUser();

        $askMsg = 'Are you sure you want to delete this tenant?';

        /** @var Property $property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')
            ->findOneBy(['id' => $propertyId, 'user' => $user]);

        if (!$property) {
            throw new NotFoundHttpException('No permissions');
        }

        if ($request->getMethod() === 'DELETE') {
            $invitedUser = $this->em->getRepository('ErpUserBundle:InvitedUser')
                ->findOneBy(['property' => $property]);

            if ($invitedUser) {
                $this->em->remove($invitedUser);
            }

            $property->setStatus(Property::STATUS_DRAFT);
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig',
            [
                'askMsg'    => $askMsg,
                'actionUrl' =>
                    $this->generateUrl('erp_property_listings_delete_invited_tenant', ['propertyId' => $propertyId])
            ]
        );
    }

    /**
     * Send repost property request
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function repostRequestAction(Request $request, $propertyId)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        if ($user->isReadOnlyUser()) {
            throw $this->createNotFoundException();
        }

        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')->find($propertyId);

        if (!$property) {
            throw new NotFoundHttpException();
        }

        $amount = $this->get('erp.core.fee.service')->getPostVacancyOnlineFee();

        if ($request->getMethod() === 'POST') {
            // Make one payment
            $customer = $user->getPaySimpleCustomers()->first();
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
                $this->addFlash(
                    'alert_error',
                    $this->get('erp.users.user.service')->getPaySimpleErrorByCode('error')
                );
            } else {
                $repostRequest = new PropertyRepostRequest();
                $repostRequest->setProperty($property);
                $repostRequest->setStatus($repostRequest::STATUS_NEW);

                $this->em->persist($repostRequest);
                $this->em->flush();

                $this->addFlash(
                    'alert_ok',
                    $this->get('erp.users.user.service')->getPaySimpleErrorByCode('charge_repost_request_ok')
                );
            }

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render(
            'ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig',
            [
                'askMsg'    => 'You will be charged $' . $amount . ' for this feature. Do you want to proceed?',
                'actionBtn' => 'Yes',
                'cancelBtn' => 'No',
                'actionUrl' => $this->generateUrl('erp_property_repost_request', ['propertyId' => $propertyId])
            ]
        );
    }

    /**
     * Create form for new|existing property
     *
     * @param Property $property
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditPropertyForm(Property $property)
    {
        $action = $this->generateUrl('erp_property_listings_add');
        if ($property->getId()) {
            $action = $this->generateUrl('erp_property_listings_edit', ['propertyId' => $property->getId()]);
        }
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new EditPropertyFormType($this->container), $property, $formOptions);

        return $form;
    }

    /**
     * Create form for property documents
     *
     * @param Property $property
     * @param int      $pageNumber
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditDocumentPropertyForm(Property $property, $pageNumber = 1)
    {
        $action = $this->generateUrl(
            'erp_property_listings_edit_documents',
            ['propertyId' => $property->getId(), 'page' => $pageNumber]
        );

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new EditDocumentPropertyFormType($this->container), $property, $formOptions);

        return $form;
    }

    /**
     * Create form for property images
     *
     * @param Property $property
     * @param int      $pageNumber
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createEditImagePropertyForm(Property $property, $pageNumber = 1)
    {
        $action = $this->generateUrl(
            'erp_property_listings_edit_images',
            ['propertyId' => $property->getId(), 'page' => $pageNumber]
        );

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new EditImagePropertyFormType($this->container), $property, $formOptions);

        return $form;
    }

    /**
     * Create property import form
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createPropertyImportFormType()
    {
        $action = $this->generateUrl('erp_property_listings_all');
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new PropertyImportFormType(), null, $formOptions);

        return $form;
    }
}
