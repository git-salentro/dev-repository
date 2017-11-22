<?php

namespace Erp\UserBundle\Twig;

use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class UserExtention
 *
 * @package Erp\UserBundle\Twig
 */
class UserExtention extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $countUnreadMessages = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('count_unread_messages', [$this, 'getCountUnreadMessages'])
        ];
    }

    /**
     * Return count unread messages
     *
     * @param User $user
     * @param User $fromUser
     *
     * @return int
     */
    public function getCountUnreadMessages(User $user, User $fromUser = null)
    {
        if ($fromUser) {
            if (!isset($this->countUnreadMessages[$fromUser->getId()])) {
                $count = $this->em->getRepository('ErpUserBundle:Message')->getCountUnreadMessages($user, $fromUser);
                $this->countUnreadMessages[$fromUser->getId()] = $count;
            } else {
                $count = $this->countUnreadMessages[$fromUser->getId()];
            }
        } else {
            $count = $this->em->getRepository('ErpUserBundle:Message')->getCountUnreadMessages($user);
        }

        return $count;
    }
}
