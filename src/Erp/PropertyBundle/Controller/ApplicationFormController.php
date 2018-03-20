<?php

namespace Erp\PropertyBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\CoreBundle\Entity\Document;
use Erp\CoreBundle\Entity\Image;
use Erp\PaymentBundle\Form\Type\PaySimpleCreditCardAnonymousFormType;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerInterface;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RecurringPaymentModel;
use Erp\PropertyBundle\Entity\ApplicationForm;
use Erp\PropertyBundle\Entity\ApplicationField;
use Erp\PropertyBundle\Entity\ApplicationSection;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Form\Type\ApplicationSectionFormType;
use Erp\PropertyBundle\Repository\ApplicationFieldRepository;
use Erp\PropertyBundle\Repository\ApplicationSectionRepository;
use Erp\PropertyBundle\Repository\ApplicationFormRepository;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\UserDocument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ApplicationFormController extends BaseController
{
    /** @var string */
    protected $baseWebDir = '/cache/pdf_images';

    /** @var string */
    protected $basePdfDir;

    /** @var string */
    protected $publicDir = '/web';

    /** @var string */
    protected $rootDir;

    /** @var string */
    protected $webDir;

    /** @var string */
    protected $pdfDir;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $document = new Document();

        $this->rootDir = $this->container->getParameter('kernel.root_dir');
        $this->webDir = $this->rootDir . '/..' . $this->publicDir . $this->baseWebDir;
        $this->basePdfDir = $document->getUploadDir();
        $this->pdfDir = $document->getUploadRootDir();
    }

    /**
     * Constructor form page
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return RedirectResponse|Response
     */
    public function cunstructorAction(Request $request, $propertyId)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        if ($user->isReadOnlyUser() and !$user->getIsApplicationFormCounterFree()) {
            throw $this->createNotFoundException();
        }

        $property = $this->getProperty($propertyId);
        /** @var ApplicationFormRepository $applicationFormRepository */
        $applicationFormRepository = $this->em->getRepository('ErpPropertyBundle:ApplicationForm');
        $applicationForm = $applicationFormRepository->findOneBy(['user' => $user]);

        // Cloning application form, if not exists yet
        if (!$applicationForm) {
            if ($user->getApplicationFormCounter() || $user->getIsApplicationFormCounterFree()) {
                if (!$user->getIsApplicationFormCounterFree()) {
                    $this->em->persist(
                        $property->getUser()->setApplicationFormCounter(
                            $property->getUser()->getApplicationFormCounter() - 1
                        )
                    );
                    $this->em->flush();
                }

                $applicationForm = $this->getCloneApplicationForm($user);

                $this->addFlash(
                    'alert_ok',
                    'Application Form was created and published on the public property page.'
                );
            } else {
                throw $this->createNotFoundException();
            }
        }

        $applicationSection = new ApplicationSection();
        $applicationSectionForm = $this->createApplicationSectionForm($applicationSection, $property);

        if ($request->getMethod() === 'POST') {
            $applicationSectionForm->handleRequest($request);

            if ($applicationSectionForm->isValid()) {
                /** @var ApplicationSectionRepository $applicationSectionRepository */
                $applicationSectionRepository = $this->em->getRepository('ErpPropertyBundle:ApplicationSection');
                $sortNumber = $applicationSectionRepository->getSortNumber($applicationForm);

                $applicationSection->setApplicationForm($applicationForm);
                $applicationSection->setSort($sortNumber);

                $this->em->persist($applicationSection);
                $this->em->flush();

                return $this->redirectToRoute('erp_property_application_form', ['propertyId' => $property->getId()]);
            }
        }

        return $this->render(
            'ErpPropertyBundle:ApplicationForm:constructor.html.twig',
            [
                'user' => $property->getUser(),
                'propertyId' => $property->getId(),
                'applicationForm' => $applicationForm,
                'applicationSectionForm' => $applicationSectionForm->createView(),
            ]
        );
    }

    /**
     * Charge for create application form
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return Response
     */
    public function constructorChargeAction(Request $request, $propertyId)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();

        $applicationFormRepository = $this->em->getRepository('ErpPropertyBundle:ApplicationForm');
        $applicationForm = $applicationFormRepository->findOneBy(['user' => $user]);

        if ($applicationForm) {
            return $this->redirectToRoute('erp_property_application_form', ['propertyId' => $propertyId]);
        }

        $amount = $this->get('erp.core.fee.service')->getCreateApplicationFormFee();

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
                $this->addFlash(
                    'alert_error',
                    $this->get('erp.users.user.service')->getPaySimpleErrorByCode('error')
                );
                $redirect = $this->redirectToRoute(
                    'erp_property_listings_edit_documents',
                    ['propertyId' => $propertyId]
                );
            } else {
                $this->addFlash(
                    'alert_ok',
                    $this->get('erp.users.user.service')->getPaySimpleErrorByCode('create_application_form_ok')
                );
                $this->em->persist(
                    $user->setApplicationFormCounter(
                        $user->getApplicationFormCounter() + 1
                    )
                );
                $this->em->flush();
                $redirect = $this->redirectToRoute('erp_property_application_form', ['propertyId' => $propertyId]);
            }

            return $redirect;
        }

        return $this->render(
            'ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig',
            [
                'actionUrl' =>
                    $this->generateUrl('erp_property_application_form_charge', ['propertyId' => $propertyId]),
                'actionBtn' => 'Yes',
                'cancelBtn' => 'No',
                'askMsg' => 'You will be charged $' . $amount . ' for this feature. Do you want to proceed?',
            ]
        );
    }

    /**
     * @param User $user
     *
     * @return null|object
     */
    public function getCloneApplicationForm(User $user)
    {
        /** @var ApplicationFormRepository $applicationFormRepository */
        $applicationFormRepository = $this->em->getRepository('ErpPropertyBundle:ApplicationForm');

        $applicationForm =
            $applicationFormRepository->findOneBy(['isDefault' => true, 'user' => null]);

        if (!$applicationForm) {
            throw new NotFoundHttpException('Default application form not found');
        }

        $this->cloneApplicationForm($applicationForm, $user);
        $this->em->clear();

        $applicationForm = $applicationFormRepository->findOneBy(['user' => $user]);

        return $applicationForm;
    }

    /**
     * Return view complete
     *
     * @param Request $request
     * @param int     $propertyId
     *
     * @return RedirectResponse|Response
     */
    public function completeAction(Request $request, $propertyId)
    {
        /** @var Property $property */
        $property = $this->getProperty($propertyId);

        if ($property->getStatus() == Property::STATUS_DELETED) {
            throw new NotFoundHttpException('Application form not found');
        }

        $psCcAnonymousForm = $this->createPaySimpleCreditCardAnonymousForm();

        $data = [];
        $psData = [];
        if ($request->getMethod() === 'POST') {
            $data = $request->request->all();
            $psData = $data['ps_cc_anonymous_form'];

            $user = $property->getUser();
            $user->addRole(User::ROLE_ANONYMOUS);

            // Make one payment
            $result = $this->get('erp.users.user.service')->makeOnePaymentAnonymous($user, $psData);
            $user->removeRole(User::ROLE_ANONYMOUS);

            $files = $request->files->all();
            if ($result['status']) {
                $files = $this->getUploadFilesInBase64($files);

                $formData = array_merge($data, $files);

                $html = $this->renderView(
                    'ErpPropertyBundle:ApplicationForm:pdf-template.html.twig',
                    [
                        'property' => $property,
                        'formData' => $formData
                    ]
                );

                $pdfFileName = $psData['firstName'] . '_' . $psData['lastName'] . '_' . date('dmYHis') . '.pdf';
                $pdfOriginalName = $psData['firstName'] . ' ' . $psData['lastName'] . '.pdf';
                $this->generatePdfFromHtml($pdfFileName, $html);

                $emailTenant = $psData['email'];

                // Create new document
                $document = (new Document())
                    ->setName($pdfFileName)
                    ->setOriginalName($pdfOriginalName)
                    ->setPath($this->basePdfDir);

                // Create new user document
                $userDocument = (new UserDocument())
                    ->setFromUser(null)
                    ->setToUser($user)
                    ->setDocument($document)
                    ->setStatus(UserDocument::STATUS_RECEIVED);

                $this->em->persist($userDocument);
                $this->em->flush();

                $this->get('erp.signature.docusign.service')->createEnvelopeFromDocument($document, $emailTenant);

                // sending email for manager
                $this->sendApplicationFormToEmail([
                    'sendTo' => $property->getUser()->getEmail(),
                    'replyTo' => $emailTenant,
                    'filename' => $pdfFileName,
                    'property' => $property,
                ]);

                $this->addFlash(
                    'alert_ok',
                    'Payment was successful. Your Rental Application is sent to Manager and a copy to you.
                     Instruction how to sign your application and link to e-signature service were sent to your email.
                     Please read instruction first then proceed to online e-signature.'
                );

                $this->sendApplicationFormInstruction([
                    'sendTo' => $emailTenant,
                    'llEmail' => $property->getUser()->getEmail(),
                    'llName' => $property->getUser()->getFirstName() . ' ' . $property->getUser()->getLastName(),
                ]);

                return $this->redirectToRoute(
                    'erp_property_application_complete_form',
                    ['propertyId' => $property->getId()]
                );
            } else {
                if ($files) {
                    $this->addFlash(
                        'alert_error',
                        'Validation error(s) occurred. Please re-upload previously attached file(s).'
                    );
                }

                /* Payment Errors */
                foreach ($result['errors'] as $error) {
                    $this->addFlash('alert_error', $error['Message']);
                }

                $psData['error']['section'] = 'payment';
            }
        }

        /* Breadcrumbs */
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('erp_site_homepage'));
        $breadcrumbs->addItem(
            $property->getName(),
            $this->get('router')->generate(
                'erp_property_page',
                [
                    'stateCode' => $property->getCity()->getStateCode(),
                    'cityName' => $property->getCity()->getName(),
                    'name' => $property->getName(),
                ]
            )
        );
        $breadcrumbs->addItem('Online Rental Application');

        if ($this->getUser() && $this->getUser()->hasRole(User::ROLE_MANAGER)) {
            $isManager = true;
        } else {
            $isManager = false;
        }

        return $this->render(
            'ErpPropertyBundle:ApplicationForm:complete.html.twig',
            [
                'data'              => $data,
                'psData'            => $psData,
                'property'          => $property,
                'isManager'        => $isManager,
                'psCcAnonymousForm' => $psCcAnonymousForm->createView()
            ]
        );
    }

    /**
     * @return PaySimpleCreditCardAnonymousFormType|\Symfony\Component\Form\Form
     */
    public function createPaySimpleCreditCardAnonymousForm()
    {
        $form = new PaySimpleCreditCardAnonymousFormType();
        $form = $this->createForm($form, null, []);

        return $form;
    }

    /**
     * Remove application section
     *
     * @param Request   $request
     * @param int       $propertyId
     * @param int       $sectionId
     *
     * @return JsonResponse
     */
    public function removeSectionAction(Request $request, $propertyId, $sectionId)
    {
        $applicationSection = $this->getApplicationSection($sectionId);

        if ($request->getMethod() === 'DELETE') {
            $this->em->remove($applicationSection);
            $this->em->flush();

            return $this->redirectToRoute('erp_property_application_form', ['propertyId' => $propertyId]);
        }

        return $this->render(
            'ErpCoreBundle:crossBlocks:delete-confirmation-popup.html.twig',
            [
                'askMsg'    => 'Are you sure you want to delete this section?',
                'actionUrl' => $this->generateUrl(
                    'erp_property_application_remove_section',
                    [
                        'propertyId' => $propertyId, 'sectionId' => $sectionId
                    ]
                )
            ]
        );
    }

    /**
     * Update data application section
     *
     * @param Request $request
     * @param int     $propertyId
     * @param int     $sectionId
     *
     * @return JsonResponse
     */
    public function updateSectionAction(Request $request, $propertyId, $sectionId)
    {
        $applicationSection = $this->getApplicationSection($sectionId);

        $name = $request->get('name');

        $validator = $this->get('validator');
        $applicationSection->setName($name);

        $errors = $validator->validate($applicationSection, null, ['ApplicationSection']);

        if (count($errors)) {
            return new JsonResponse(['errors' => 'Section name isn\'t correct']);
        }

        $this->em->persist($applicationSection);
        $this->em->flush();

        return new JsonResponse(['status' => true]);
    }

    /**
     * Adding field
     *
     * @param Request   $request
     * @param int       $propertyId
     * @param int       $sectionId
     *
     * @return JsonResponse
     */
    public function addFieldAction(Request $request, $propertyId, $sectionId)
    {
        $applicationSection = $this->getApplicationSection($sectionId);
        $validator = $this->get('validator');
        /** @var ApplicationField $applicationField */
        $applicationField = new ApplicationField();

        $type = $request->get('type');
        $name = ($type !== ApplicationField::TYPE_RADIO) ? $request->get('name') : null;
        $data = $request->get('data', false);
        $data = ($data) ? json_encode($data, JSON_FORCE_OBJECT) : null;

        $applicationField->setName($name);
        $applicationField->setType($type);
        $applicationField->setData($data);
        $applicationField->setApplicationSection($applicationSection);

        /** @var ApplicationFieldRepository $applicationFieldRepository */
        $applicationFieldRepository = $this->em->getRepository('ErpPropertyBundle:ApplicationField');
        $sortNumber = $applicationFieldRepository->getSortNumber($applicationSection);

        $applicationField->setSort($sortNumber);

        $validationGroup = ($type === ApplicationField::TYPE_RADIO) ? 'ApplicationFieldRadio': 'ApplicationField';
        $errors = $validator->validate($applicationField, null, $validationGroup);
        if (count($errors)) {
            return new JsonResponse(['errors' => 'Field name or type isn\'t correct']);
        }

        $this->em->persist($applicationField);
        $this->em->flush();

        $applicationSectionRender = $this->render(
            'ErpPropertyBundle:ApplicationForm:blocks/section.html.twig',
            [
                'propertyId' => $propertyId,
                'applicationSection' => $applicationSection
            ]
        );

        return new JsonResponse(json_decode($applicationSectionRender->getContent()));
    }

    /**
     * Removing field
     *
     * @param int $propertyId
     * @param int $fieldId
     *
     * @return JsonResponse
     */
    public function removeFieldAction($propertyId, $fieldId)
    {
        $applicationField = $this->getApplicationField($propertyId, $fieldId);

        $this->em->remove($applicationField);
        $this->em->flush();

        return new JsonResponse(['status' => true]);
    }

    /**
     * Return property
     *
     * @param int $propertyId
     *
     * @return Property
     */
    protected function getProperty($propertyId)
    {
        /** @var User $user */
        $user = $this->getUser();

        $propertyRepository = $this->em->getRepository('ErpPropertyBundle:Property');

        /** @var Property $property */
        $property = ($user instanceof User and $user->hasRole(User::ROLE_MANAGER))
            ? $propertyRepository->findOneBy(['id' => $propertyId, 'user' => $user])
            : $propertyRepository->find($propertyId);

        if (!$property instanceof Property) {
            throw new NotFoundHttpException('Property not found');
        }

        return $property;
    }

    /**
     * Return application section
     *
     * @param int $sectionId
     *
     * @return ApplicationSection
     */
    protected function getApplicationSection($sectionId)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var ApplicationFormRepository $applicationFormRepository */
        $applicationFormRepository = $this->em->getRepository('ErpPropertyBundle:ApplicationForm');
        $applicationForm = $applicationFormRepository->findOneBy(['user' => $user]);

        /** @var ApplicationSectionRepository $applicationSectionRepository */
        $applicationSectionRepository = $this->em->getRepository('ErpPropertyBundle:ApplicationSection');
        /** @var ApplicationSection $applicationSection */
        $applicationSection =
            $applicationSectionRepository->findOneBy(['id' => $sectionId, 'applicationForm' => $applicationForm]);

        if (!$applicationSection) {
            throw new NotFoundHttpException('Section not found');
        }

        return $applicationSection;
    }

    /**
     * Return application field
     *
     * @param int $propertyId
     * @param int $fieldId
     *
     * @return ApplicationField
     */
    protected function getApplicationField($propertyId, $fieldId)
    {
        $this->getProperty($propertyId);
        /** @var ApplicationFieldRepository $applicationFieldRepository */
        $applicationFieldRepository = $this->em->getRepository('ErpPropertyBundle:ApplicationField');
        /** @var ApplicationField $applicationField */
        $applicationField = $applicationFieldRepository->find($fieldId);

        if (!$applicationField instanceof ApplicationField) {
            throw new NotFoundHttpException('Field not found');
        }

        return $applicationField;
    }

    /**
     * Create application section form
     *
     * @param ApplicationSection $applicationSection
     * @param Property           $property
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createApplicationSectionForm(ApplicationSection $applicationSection, Property $property)
    {
        $action = $this->generateUrl('erp_property_application_form', ['propertyId' => $property->getId()]);
        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new ApplicationSectionFormType(), $applicationSection, $formOptions);

        return $form;
    }

    /**
     * Clone application form
     *
     * @param ApplicationForm $applicationForm
     * @param User            $user
     *
     * @return ApplicationForm
     */
    protected function cloneApplicationForm(ApplicationForm $applicationForm, User $user)
    {
        /** @var ApplicationForm $applicationFormClone */
        $applicationFormClone = clone $applicationForm;
        $applicationFormClone->setUser($user);

        $this->em->persist($applicationFormClone);
        $this->em->flush();

        return $applicationFormClone;
    }

    /**
     * Generate pdf file from html
     *
     * @param string $fileName
     * @param string $html
     *
     * @return mixed
     */
    protected function generatePdfFromHtml($fileName, $html)
    {
        /** @var $dompdf \Slik\DompdfBundle\Wrapper\DompdfWrapper */
        define("DOMPDF_ENABLE_CSS_FLOAT", true);

        $dompdf = $this->get('slik_dompdf');
        $dompdf->getpdf($html);
        $output = $dompdf->output();

        if (!file_exists($this->pdfDir)) {
            mkdir($this->pdfDir);
        }

        $result = file_put_contents($this->pdfDir . '/' . $fileName, $output);

        return $result;
    }

    /**
     * Return array files in base64
     *
     * @param array $uploadFiles
     *
     * @return mixed
     */
    protected function getUploadFilesInBase64($uploadFiles)
    {
        foreach ($uploadFiles as $key => $file) {
            /** @var UploadedFile $file */
            if ($file instanceof UploadedFile && in_array($file->getMimeType(), Image::$allowedMimeTypes)) {
                $uploadFileExtension = $file->guessExtension();
                $uploadFileName = sha1(uniqid(mt_rand(), true)) . '.' . $uploadFileExtension;

                $file->move($this->webDir, $uploadFileName);

                $dataFile = file_get_contents($this->webDir . '/' . $uploadFileName);

                $base64 = 'data:image/' . $uploadFileExtension . ';base64,' . base64_encode($dataFile);
                $uploadFiles[$key] = [
                    'fileBase64' => $base64,
                ];
            } else {
                $uploadFiles[$key] = null;
            }
        }

        return $uploadFiles;
    }

    /**
     * Send application form
     *
     * @param array $params
     *
     * @return mixed
     */
    protected function sendApplicationFormToEmail($params)
    {
        $url = $this->get('router')->generate(
            'erp_property_page',
            [
                'stateCode' => $params['property']->getCity()->getStateCode(),
                'cityName'  => $params['property']->getCity()->getName(),
                'name'      => $params['property']->getName(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $emailParams = [
            'sendTo' => $params['sendTo'],
            'replyTo' => $params['replyTo'],
            'filename' => $params['filename'],
            'pdfDir' => $this->pdfDir,
            'url' => $url,
        ];

        $emailType = EmailNotificationFactory::TYPE_APPLICATION_FORM_TO_MANAGER;
        $sentStatus = $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $sentStatus;
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    protected function sendApplicationFormInstruction($params)
    {
        $emailParams = [
            'sendTo' => $params['sendTo'],
            'llEmail' => $params['llEmail'],
            'llName' => $params['llName'],
        ];

        $emailType = EmailNotificationFactory::TYPE_APPLICATION_FORM_INSTRUCTION;
        $sentStatus = $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $sentStatus;
    }
}
