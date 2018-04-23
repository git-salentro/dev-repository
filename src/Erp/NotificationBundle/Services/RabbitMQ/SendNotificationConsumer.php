<?php

namespace Erp\NotificationBundle\Services\RabbitMQ;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use PhpAmqpLib\Message\AMQPMessage;
use Erp\NotificationBundle\Entity\History;
use Erp\UserBundle\Entity\User;
use Erp\PropertyBundle\Entity\Property;

class SendNotificationConsumer implements ConsumerInterface
{
    public function setContainer(Container $container)
    {
        $this->container = $container;
        return $this;
    }

    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);
        if (!is_array($data)) {
            $this->logDataError($data);
            return;
        }

        $userEm = $this->container->get('doctrine')->getManagerForClass(User::class);
        $historyEm = $this->container->get('erp_notification.history_entity_manager');
        $mailer = $this->container->get('erp_user.mailer.processor');
        $historyManager = $this->container->get('erp_notification.history_manager');

        if ($mailer->sendCustomEmail($data['mailTo'], $data['mailFrom'], $data['data']['title'], $data['rendered'])) {
            $fields = $data['data'];
            $fields['tenant'] = $userEm->getRepository(User::class)->find($data['tenantUser']);
            $fields['property'] = $userEm->getRepository(Property::class)->find($data['property']);
            
            $history = $historyManager->create($fields);

            $historyEm->persist($history);
            $historyEm->flush();

            $this->logSuccess($data, $data['prefix']);
        } else {
            $this->logEmailError($data);
        }
    }

    private function logSuccess($data, string $prefix = 'unknown')
    {
        $msg =
            'Success '.$prefix.' pay date.'."\n".
            'Data: '.var_export($data, true)."\n";
        $this->container->get('erp_notification.logger')->info($msg);
    }

    private function logEmailError($data, string $prefix = 'unknown')
    {
        $msg = '=============================='."\n".
            'Cannot send an email (trying to send '.$prefix.' pay date).'."\n".
            'Data: '.var_export($data, true)."\n".
            '==============================';
        $this->container->get('erp_notification.logger')->error($msg);
    }

    private function logDataError($data, string $prefix = '~unknown~')
    {
        $msg = '=============================='."\n".
            'Cannot parse data (trying to send '.$prefix.' pay date).'."\n".
            'Data: '.var_export($data, true)."\n".
            '==============================';
        $this->container->get('erp_notification.logger')->error($msg);
    }
}
