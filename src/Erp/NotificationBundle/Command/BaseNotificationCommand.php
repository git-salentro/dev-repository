<?php

namespace Erp\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Erp\PropertyBundle\Entity\Property;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\NotificationBundle\Entity\History;
use Erp\NotificationBundle\Entity\Template;

class BaseNotificationCommand extends ContainerAwareCommand
{
    const TYPE_NOTIFICATION = 'notification';
    const TYPE_ALERT = 'alert';

    const TYPES = [
        self::TYPE_NOTIFICATION,
        self::TYPE_ALERT,
    ];

    protected $prefix = '';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('erp:notification');
    }

    /**
     * @inheritdoc
     */
    protected function process(string $type)
    {
        if (!in_array($type, self::TYPES)) {
            return -1;
        }
        $i = 0;
        $k = 0;
        $batchSize = 20;

        $userNotificationEm = $this->getEntityManager(UserNotification::class);
        $propertyEm = $this->getEntityManager(Property::class);
        $historyEm = $this->getEntityManager(History::class);
        $historyManager = $this->getContainer()->get('erp_notification.history_manager');
        $templateManager = $this->getContainer()->get('erp_notification.template_manager');
        $mailer = $this->getContainer()->get('erp_user.mailer.processor');

        $mailFrom = $this->getContainer()->getParameter('contact_email');

        $method = null;
        if ($type === self::TYPE_NOTIFICATION) {
            $this->prefix = 'notification before';
            $method = 'getPropertiesFromNotificationsIterator';
        } elseif ($type === self::TYPE_ALERT) {
            $this->prefix = 'alert after';
            $method = 'getPropertiesFromAlertsIterator';
        }

        if ($iterableResult = $userNotificationEm->getRepository(UserNotification::class)->{$method}()) {
            foreach ($iterableResult as $propertyResult) {
                $data = reset($propertyResult);
                if ($property = $propertyEm->getRepository(Property::class)->find($data['propertyId'])) {
                    $tenant = $property->getTenantUser();
                    try {
                        $rendered = $templateManager->renderTemplateById($data['templateId']);
                    } catch (\Exception $ex) {
                        $this->logRenderError($ex, $data);
                        continue;
                    }
                    if ($mailer->sendCustomEmail($tenant->getEmail(), $mailFrom, $data['title'], $rendered)) {
                        $i++;
                        $fields = $data;
                        $fields['property'] = $property;
                        $fields['tenant'] = $tenant;
                        
                        $history = $historyManager->create($fields);

                        $historyEm->persist($history);

                        $this->logSuccess($data);

                        if (($k++ % $batchSize) === 0) {
                            $historyEm->flush();
                            $historyEm->clear();
                        }
                    } else {
                        $data['mailTo'] = $tenant->getEmail();
                        $this->logEmailError($data);
                    }
                }
            }
        }
        $historyEm->flush();
        $historyEm->clear();
        return $i.' tenants ('.$this->prefix.' payment date).';
    }

    private function getEntityManager($class)
    {
        $repository = $this->getContainer()->get('doctrine')->getManagerForClass($class);

        return $repository;
    }

    private function logRenderError(\Exception $ex, $data)
    {
        $msg = '=============================='."\n".
            'Render error occured for Template (trying to send '.$this->prefix.' pay date).'."\n".
            'Data: '.var_export($data, true)."\n".
            'Error message: '.$ex->getMessage()."\n".
            '==============================';
        $this->getContainer()->get('erp_notification.logger')->error($msg);
    }

    private function logSuccess($data)
    {
        $msg =
            'Success '.$this->prefix.' pay date.'."\n".
            'Data: '.var_export($data, true)."\n";
        $this->getContainer()->get('erp_notification.logger')->info($msg);
    }

    private function logEmailError($data)
    {
        $msg = '=============================='."\n".
            'Cannot send an email (trying to send '.$this->prefix.' pay date).'."\n".
            'Data: '.var_export($data, true)."\n".
            '==============================';
        $this->getContainer()->get('erp_notification.logger')->error($msg);
    }
}
