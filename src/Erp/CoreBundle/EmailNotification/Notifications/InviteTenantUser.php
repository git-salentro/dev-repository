<?php

namespace Erp\CoreBundle\EmailNotification\Notifications;

use Erp\CoreBundle\EmailNotification\AbstractEmailNotification;
use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;

class InviteTenantUser extends AbstractEmailNotification
{
    /**
     * @var string
     */
    protected $type = EmailNotificationFactory::TYPE_INVITE_TENANT_USER;

    /**
     * Send email notification when new Administrator created
     *
     * @param array $params
     */
    public function sendEmailNotification($params)
    {
        /** @var $contailner \Symfony\Component\DependencyInjection\ContainerInterface */
        $contailner = $params['container'];

        $message = $params['mailer']->createMessage()
            ->setFrom([$params['mailFrom'] => 'eRentPay'])
            ->setTo($params['sendTo'])
            ->setContentType('text/html');

        $template = 'ErpCoreBundle:EmailNotification:' . $this->getType() . '.html.twig';
        $emailParams = [
            'url'       => $params['url'],
        ];

        $emailParams['imageErp'] = $message->embed($this->getLogoPath($params));

        $body = $contailner->get('templating')->render($template, $emailParams);
        $message->setSubject('eRentPay - You were registered as Tenant of Property by Landlord')->setBody($body);
        $result = $params['mailer']->send($message);

        return $result;
    }
}
