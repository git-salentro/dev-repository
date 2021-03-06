<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\SiteBundle\Entity\StaticPage;
use Erp\UserBundle\Entity\InvitedUser;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Form\Type\LandlordRegistrationFormType;

use Erp\UserBundle\Form\Type\UserTermOfUseFormType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RegistrationController
 *
 * @package Erp\UserBundle\Controller
 */
class RegistrationController extends BaseController
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * This used instead of __construct as Symfony2 controllers don't support constructors by default
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->em = $this->getDoctrine()->getManager();
    }

    /**
     * Register landlord
     *
     * @param Request $request
     *
     * @return null|RedirectResponse|Response
     */
    public function registerAction(Request $request)
    {
        if ($this->getUser()) {
            return $this->redirect($this->generateUrl('erp_user_profile_dashboard'));
        }
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var User $user */
        $user = $userManager->createUser();
        $user->setRoles([User::ROLE_LANDLORD]);

        $form = $this->createRegisterForm($request, $user);
        $isRegisterAccept = false;

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if (!$user->getConfirmationToken()) {
                    /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                    $tokenGenerator = $this->get('fos_user.util.token_generator');
                    $user->setConfirmationToken($tokenGenerator->generateToken());
                }

                $settings = $this->get('erp.users.user.service')->getSettings();

                $user->setUsername($user->getEmail())
                    ->setStatus(User::STATUS_PENDING)
                    ->setSettings(array_keys($settings))
                    ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
                    ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
                    ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
                    ->setIsPrivatePaySimple(0)
                    ->setIsApplicationFormCounterFree(1)
                    ->setIsPropertyCounterFree(1)
                ;

                $userManager->updateUser($user);
                $isRegisterAccept = true;

                $this->sendRegistrationEmail($user);
            }
        } else {
            $form->get('email')->setData($request->get('email', null));
        }

        $termsOfUse =$this->em->getRepository('ErpSiteBundle:StaticPage')
            ->findOneBy(['code' => StaticPage::PAGE_CODE_TERMS_OF_USE]);

        return $this->render(
            'ErpUserBundle:Registration:register.html.twig',
            [
                'form'             => $form->createView(),
                'isRegisterAccept' => $isRegisterAccept,
                'user'             => $user,
                'termsOfUse'       => $termsOfUse->getContent(),
                'roleLandlord'     => User::ROLE_LANDLORD,
            ]
        );
    }

    public function termOfUseAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not authorized');
        }

        $form = $this->createUserTermOfUseForm($user);

        if ($request->getMethod() == 'POST') {
            $user->setIsTermOfUse(true);
            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        $termsOfUse = $this->em->getRepository('ErpSiteBundle:StaticPage')
            ->findOneBy(['code' => StaticPage::PAGE_CODE_TERMS_OF_USE]);

        return $this->render(
            'ErpUserBundle:Registration:term-of-use.html.twig',
            [
                'form'             => $form->createView(),
                'termsOfUse'       => $termsOfUse->getContent(),
            ]
        );
    }

    /**
     * Confirm landlord registration
     *
     * @param Request $request
     * @param string  $token
     *
     * @return RedirectResponse
     * @throws NotFoundHttpException
     */
    public function setConfirmRegisterAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            $msg = sprintf('The user with "confirmation token" does not exist for value "%s"', $token);
            throw new NotFoundHttpException($msg);
        }

        if ($user->getStatus() == User::STATUS_DISABLED || $user->getStatus() == User::STATUS_REJECTED) {
            throw new NotFoundHttpException('Account is disabled. Please contact site Administrator.');
        }

        $user
            ->setEnabled(true)
            ->setStatus(User::STATUS_PENDING)
            ->setConfirmationToken(null);
        $userManager->updateUser($user);

        $response = new RedirectResponse($this->generateUrl('erp_site_homepage'));
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher
            ->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Registration for tenant
     *
     * @param Request $request
     * @param string  $token
     *
     * @return null|Response
     */
    public function registerTenantAction(Request $request, $token)
    {
        /** @var $invitedUser \Erp\UserBundle\Entity\InvitedUser */
        $invitedUser = $this->em->getRepository('ErpUserBundle:InvitedUser')->findOneBy(
            ['inviteCode' => $token, 'isUse' => false]
        );
        $landlordUser = $invitedUser ? $invitedUser->getProperty()->getUser() : null;

        if (!$invitedUser || !$landlordUser || !$landlordUser->hasRole(User::ROLE_LANDLORD)) {
            return $this->redirectToRoute('fos_user_security_login');
        }

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setRoles([User::ROLE_TENANT])
            ->setEnabled(true)
            ->setEmail($invitedUser->getInvitedEmail());
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        $form = $this->createRegisterForm($request, $user, $invitedUser);

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $this->em->persist($invitedUser->setIsUse(true));

                $user->setUsername($user->getEmail())
                    ->setStatus(User::STATUS_ACTIVE)
                    ->setPropertyCounter(User::DEFAULT_PROPERTY_COUNTER)
                    ->setApplicationFormCounter(User::DEFAULT_APPLICATION_FORM_COUNTER)
                    ->setContractFormCounter(User::DEFAULT_CONTRACT_FORM_COUNTER)
                    ->setIsPrivatePaySimple(0)
                ;
                $userManager->updateUser($user);

                $this->em->persist($invitedUser->getProperty()->setTenantUser($user));
                $this->em->remove($invitedUser);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('erp_site_homepage');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(
                    FOSUserEvents::REGISTRATION_COMPLETED,
                    new FilterUserResponseEvent($user, $request, $response)
                );

                return $response;
            }
        }

        $termsOfUse = $this->em->getRepository('ErpSiteBundle:StaticPage')
            ->findOneBy(['code' => StaticPage::PAGE_CODE_TERMS_OF_USE]);

        return $this->render(
            'ErpUserBundle:Registration:register.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
                'termsOfUse' => $termsOfUse->getContent(),
                'roleLandlord' => User::ROLE_LANDLORD,
            ]
        );
    }

    /**
     * Create Register new Landlord form
     *
     * @param Request     $request
     * @param User        $user
     * @param InvitedUser $invitedUser
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createRegisterForm(Request $request, User $user, InvitedUser $invitedUser = null)
    {
        if ($invitedUser) {
            $action = $this->generateUrl('erp_user_tenant_registration', ['token' => $invitedUser->getInviteCode()]);
        } else {
            $action = $this->generateUrl('fos_user_registration_register');
        }

        $formOptions = [
            'action' => $action,
            'method' => 'POST',
        ];

        $states = $this->get('erp.core.location')->getStates();

        $form = $this->createForm(
            new LandlordRegistrationFormType($request, $states, (bool)$invitedUser),
            $user,
            $formOptions
        );

        return $form;
    }

    /**
     * Create User Term Of Use Form Type
     *
     * @param User $user
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createUserTermOfUseForm(User $user)
    {
        $action = $this->generateUrl('erp_user_term_of_use');

        $formOptions = [
            'action' => $action,
            'method' => 'POST',
        ];

        $form = $this->createForm(
            new UserTermOfUseFormType(),
            $user,
            $formOptions
        );

        return $form;
    }

    /**
     * Sent email after landlord registration
     *
     * @param User $user
     *
     * @return bool
     */
    protected function sendRegistrationEmail(User $user)
    {
        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url' => $this->generateUrl(
                'erp_user_registration_set_confirm',
                ['token' => $user->getConfirmationToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ];
        $emailType = EmailNotificationFactory::TYPE_LANDLORD_USER_REGISTER;
        $sentStatus = $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $sentStatus;
    }
}
