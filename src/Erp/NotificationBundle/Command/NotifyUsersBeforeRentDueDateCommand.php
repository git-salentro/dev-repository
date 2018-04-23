<?php

namespace Erp\NotificationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Erp\PropertyBundle\Entity\Property;
use Erp\NotificationBundle\Entity\UserNotification;
use Erp\NotificationBundle\Entity\History;
use Erp\NotificationBundle\Entity\Template;

class NotifyUsersBeforeRentDueDateCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('erp:notification:notify-users-before-rent-due-date')
            ->setDescription('Notify users before rent due date');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        if ($iterableResult = $userNotificationEm->getRepository(UserNotification::class)->getPropertiesFromNotificationsIterator()) {
            foreach ($iterableResult as $propertyResult) {
                $i++;
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
        $output->writeln($i.' tenants were notified before payment date.');
    }

    private function getEntityManager($class)
    {
        $repository = $this->getContainer()->get('doctrine')->getManagerForClass($class);

        return $repository;
    }

    private function logRenderError(\Exception $ex, $data)
    {
        $msg = '=============================='."\n".
            'Render error occured for Template (trying to send notification before pay date).'."\n".
            'Data: '.var_export($data, true)."\n".
            'Error message: '.$ex->getMessage()."\n".
            '==============================';
        $this->getContainer()->get('erp_notification.logger')->error($msg);
    }

    private function logSuccess($data)
    {
        $msg = "\n".
            'Success notification before pay date.'."\n".
            'Data: '.var_export($data, true)."\n";
        $this->getContainer()->get('erp_notification.logger')->info($msg);
    }

    private function logEmailError($data)
    {
        $msg = '=============================='."\n".
            'Cannot send an email (trying to send notification before pay date).'."\n".
            'Data: '.var_export($data, true)."\n".
            '==============================';
        $this->getContainer()->get('erp_notification.logger')->error($msg);
    }
}
