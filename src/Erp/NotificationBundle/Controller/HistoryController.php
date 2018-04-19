<?php

namespace Erp\NotificationBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HistoryController extends BaseController
{
    /**
     * @Template()
     */
    public function listAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $historyItems = $this->getHistoryRepository()->findAll();

        return [
            'historyItems' => $historyItems,
        ];
    }

    private function getHistoryRepository()
    {
        return $this->get('erp_notification.history_repository');
    }
}
