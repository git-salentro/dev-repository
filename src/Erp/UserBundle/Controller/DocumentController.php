<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Entity\Document;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\UserDocument;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\UserDocumentFormType;
use Erp\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Regex as RegexConstraints;

/**
 * Class DocumentController
 *
 * @package Erp\UserBundle\Controller
 */
class DocumentController extends BaseController
{
    /**
     * @param Request $request
     * @param int     $toUserId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \HttpException
     */
    public function indexAction(Request $request, $toUserId)
    {
        $user = $this->getUser();
        $companions = $this->getCompanions($user);

        if ($toUserId === null && count($companions)) {
            return $this->redirectToRoute('erp_user_documentation', ['toUserId' => array_shift($companions)->getId()]);
        }

        /** @var User $toUser */
        $toUser = $companions[(int)$toUserId];

        if (!$toUser
            || ($user->hasRole(User::ROLE_MANAGER)
                && !$user->isTenant($toUser)
                && !$toUser->hasRole(User::ROLE_ANONYMOUS)
            )
            || ($user->hasRole(User::ROLE_TENANT) && $user->getTenantProperty()->getUser() != $toUser)
        ) {
            throw $this->createNotFoundException();
        } else {
            $userDocument = new UserDocument();
            $form = $this->createUserDocumentForm($userDocument, $toUser);

            if ($request->getMethod() === 'POST') {
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $documentStatus = ($toUser->hasRole(User::ROLE_ANONYMOUS))
                        ? UserDocument::STATUS_RECIEVED
                        : UserDocument::STATUS_SENT;

                    $userDocument->setStatus($documentStatus);
                    $userDocument->setFromUser(($toUser->hasRole(User::ROLE_ANONYMOUS)) ? null: $user);
                    $userDocument->setToUser(($toUser->hasRole(User::ROLE_ANONYMOUS)) ? $user: $toUser);

                    $this->em->persist($userDocument);
                    $this->em->flush();

                    $this->get('session')->getFlashBag()->add('alert_ok', 'Document updoaded successfully.');

                    return $this->redirectToRoute('erp_user_documentation', ['toUserId' => $toUserId]);
                } else {
                    $this->get('session')->getFlashBag()->add('alert_error', 'Uploading of document is failed.');
                }
            }

            foreach ($companions as $key => $companion) {
                $companions[$key] = [
                    $companion,
                    'totalUserDocuments' => $this->getTotalUserDocumentsByToUser($user, $companion)
                ];
            }

            $userDocuments = $this->getUserDocuments($user, $toUser);
            $esignFee = $this->get('erp.core.fee.service')->getESignFee();

            $renderParams = [
                'form'  => $form->createView(),
                'user' => $user,
                'companions' => $companions,
                'currentCompanion' => $toUser,
                'userDocuments' => $userDocuments,
                'constRoleTenant' => User::ROLE_TENANT,
                'constRoleManager' => User::ROLE_MANAGER,
                'constRoleAnonymous' => User::ROLE_ANONYMOUS,
                'esignFee' => $esignFee,
            ];
        }

        return $this->render('ErpUserBundle:Documentation:documentation.html.twig', $renderParams);
    }

    /**
     * Update user document
     *
     * @param Request $request
     * @param int     $documentId
     *
     * @return JsonResponse
     */
    public function updateAction(Request $request, $documentId)
    {
        $userDocumentName = $request->get('fileName');
        $userDocumentStatus = $request->get('fileStatus', null);

        /** @var UserDocument $userDocument */
        $userDocument = $this->getDoctrine()
            ->getRepository('ErpUserBundle:UserDocument')
            ->findOneBy(['id' => $documentId]);

        if (!$userDocument) {
            return new JsonResponse(['error' => 'File not found']);
        }

        $regexConstraints = new RegexConstraints(
            [
                'pattern' => Document::$patternFilename,
                'message' => Document::$messagePatternFilename
            ]
        );

        /** @var $errors \Symfony\Component\Validator\ConstraintViolationListInterface */
        $errors = $this->get('validator')->validateValue(
            $userDocumentName,
            $regexConstraints
        );

        if ($errors->count() || !strlen(trim($userDocumentName))) {
            return new JsonResponse(['errors' => 'The file name must not be empty and must contain an extension']);
        }

        $userDocument->getDocument()->setOriginalName($userDocumentName);
        if ($userDocumentStatus) {
            $userDocument->setStatus($userDocumentStatus);
        }

        $this->em->persist($userDocument);
        $this->em->flush();

        return new JsonResponse(['status' => true]);
    }

    /**
     * Delete user document
     *
     * @param Request $request
     * @param int $documentId
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $documentId)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var UserDocument $userDocument */
        $userDocument = $this->em->getRepository('ErpUserBundle:UserDocument')->find($documentId);
        if (!$userDocument) {
            $renderOptions = [
                'askMsg' => 'File not found',
                'hideActionBtn' => true,
                'cancelBtn' => 'Ok'
            ];
        } elseif (!$user->hasRole(User::ROLE_MANAGER) && $userDocument->getFromUser()->getId() != $user->getId()) {
            $renderOptions = [
                'askMsg' => 'Not permissions',
                'hideActionBtn' => true,
                'cancelBtn' => 'Ok'
            ];
        } else {
            $renderOptions = [
                'askMsg' => 'Are you sure you want to delete document?',
                'actionBtn' => 'Delete',
                'actionUrl' => $this->generateUrl(
                    'erp_user_document_delete',
                    ['documentId' => $documentId]
                ),
                'actionMethod' => 'DELETE'
            ];
        }

        if ($request->getMethod() === 'DELETE') {
            $this->em->remove($userDocument);
            $this->em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('ErpCoreBundle:crossBlocks:general-confirmation-popup.html.twig', $renderOptions);
    }

    /**
     * Create user document form
     *
     * @param UserDocument $userDocument
     * @param User $toUser
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createUserDocumentForm(UserDocument $userDocument, User $toUser)
    {
        $action = $this->generateUrl('erp_user_documentation', ['toUserId' => $toUser->getId()]);

        $formOptions = ['action' => $action, 'method' => 'POST'];
        $form = $this->createForm(new UserDocumentFormType(), $userDocument, $formOptions);

        return $form;
    }

    /**
     * Return list user documents
     *
     * @param User $user
     * @param User $toUser
     *
     * @return mixed
     */
    public function getUserDocuments(User $user, User $toUser)
    {
        $userDocuments = $this->em->getRepository('ErpUserBundle:UserDocument')->getUserDocuments($user, $toUser);

        $document = new Document();
        $uploadBaseDir = $document->getUploadBaseDir($this->container);

        /** @var UserDocument $userDocument */
        foreach ($userDocuments as $userDocument) {
            $filePath = $userDocument->getDocument()->getPath();
            $fileName = $userDocument->getDocument()->getName();
            $filePath = $uploadBaseDir . $filePath . '/' .$fileName;

            if (!file_exists($filePath)) {
                $this->em->remove($userDocument);
                $this->em->flush();
            }
        }

        return $userDocuments;
    }

    /**
     * Return count documents for user
     *
     * @param User $fromUser
     * @param User $toUser
     *
     * @return int
     */
    public function getTotalUserDocumentsByToUser(User $fromUser, User $toUser)
    {
        return $this->em->getRepository('ErpUserBundle:UserDocument')
            ->getTotalUserDocumentsByToUser($fromUser, $toUser);
    }

    /**
     * Return list companions
     *
     * @param User $user
     *
     * @return array
     */
    public function getCompanions(User $user)
    {
        $companions = [];

        if ($user->hasRole(User::ROLE_MANAGER)) {
            // For anonymous area
            $companions[0] = (new User())
                ->setId(0)
                ->setFirstName('Applicants')
                ->addRole(User::ROLE_ANONYMOUS)
            ;

            $properties = $user->getProperties();
            /** @var Property $property */
            foreach ($properties as $property) {
                if ($property->getTenantUser()) {
                    $companions[$property->getTenantUser()->getId()] = $property->getTenantUser();
                }
            }
        } else {
            if ($user->getTenantProperty()) {
                $companions[$user->getTenantProperty()->getUser()->getId()] = $user->getTenantProperty()->getUser();
            }
        }

        return $companions;
    }
}
