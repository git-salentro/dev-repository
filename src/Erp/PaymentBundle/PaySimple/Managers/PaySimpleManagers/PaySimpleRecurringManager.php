<?php

namespace Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagers;

use Erp\PaymentBundle\PaySimple\Managers\PaySimpleAbstarctManager;
use Erp\PaymentBundle\PaySimple\Exceptions\PaySimpleManagerException;
use Erp\UserBundle\Entity\User;

class PaySimpleRecurringManager extends PaySimpleAbstarctManager
{
    const URI_RECURRING = '/v4/recurringpayment';
    const URI_SCHEDULE  = '/v4/paymentschedule';
    const URI_PLAN  = '/v4/paymentplan';

    /**
     * @param string $method
     *
     * @return string
     * @throws PaySimpleManagerException
     */
    public function proccess($method = '')
    {
        switch ($method) {
            case self::METHOD_RECURRING_CREATE:
            case self::METHOD_RECURRING_ONE_TIME:
                $responce = $this->create($method);
                break;
            case self::METHOD_RECURRING_SUSPEND:
                $responce = $this->suspend();
                break;
            case self::METHOD_RECURRING_GET:
                $responce = $this->retrieve();
                break;
            case self::METHOD_RECURRING_LIST:
                $responce = $this->getList();
                break;
            default:
                $available = [
                    self::METHOD_RECURRING_CREATE,
                    self::METHOD_RECURRING_SUSPEND,
                    self::METHOD_RECURRING_ONE_TIME,
                    self::METHOD_RECURRING_GET,
                    self::METHOD_RECURRING_LIST,
                ];
                throw new PaySimpleManagerException(
                    sprintf(
                        'PaySimple recurring payment method %s not found. Available methods are: %s',
                        $method,
                        implode(', ', $available)
                    )
                );
                break;
        }

        return $responce;
    }

    /**
     * Get list payment schedules (list recurring)
     *
     * @return array
     */
    protected function getList()
    {
        $params = [
            'status'  => 'active',
            'lite'     => true,
            'pagesize' => 1000,
            'page'     => 1,
        ];
        $response = $this->curl->execute(self::$apiEndpoint . self::URI_SCHEDULE . '?' . http_build_query($params));

        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * @return array
     */
    protected function create($method)
    {
        $params = $method === self::METHOD_RECURRING_CREATE ? $this->prepareCreateData() : $this->prepareOneTimeData();
        $response = $this->curl->setPostParams(json_encode($params))->execute(self::$apiEndpoint . self::URI_RECURRING);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * @return array
     */
    protected function suspend()
    {
        $recurringId = $this->model->getPsReccuringPayment()->getRecurringId();
        $url = self::$apiEndpoint . self::URI_RECURRING . '/' . $recurringId . '/suspend';

        $response = $this->curl->execute($url, 'PUT');
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Retrieve a Payment Recurring Schedule
     *
     * @return array
     */
    private function retrieve()
    {
        $recurringId = $this->model->getPsReccuringPayment()->getRecurringId();
        $response = $this->curl->execute(self::$apiEndpoint . self::URI_RECURRING . '/' . $recurringId);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Set params for creating new Recurring Payment
     *
     * @return array
     */
    private function prepareCreateData()
    {
        $customer = $this->model->getCustomer();
        $accountId = $customer->getPrimaryType() === self::CREDIT_CARD ? $customer->getCcId() : $customer->getBaId();
        $date = $this->model->getStartDate();
        /** @var $lastRecurring \Erp\PaymentBundle\Entity\PaySimpleRecurringPayment */
        $lastRecurring = $this->model->getPsReccuringPayment();
        $isPreferences = false;
        if ($lastRecurring) {
            $isPreferences = $customer->getPrimaryType() != $lastRecurring->getSubscriptionType();
        }

        if ($lastRecurring && $isPreferences) {
            $date = $lastRecurring->getNextDate();
        }

        $currentDay = (int)$date->format('d');
        $lastDays = [28, 29, 30, 31];

        $params = [
            'AccountId'                   => $accountId,
            'PaymentAmount'               => $this->model->getAmount() + $this->model->getAllowance(),
            'StartDate'                   => $date->format('Y-m-d'),
            'ExecutionFrequencyType'      => 5,
            'ExecutionFrequencyParameter' => $currentDay,
        ];

        if (in_array($currentDay, $lastDays)) {
            $params['ExecutionFrequencyType'] = 6;
            unset($params['ExecutionFrequencyParameter']);
        }

        return $params;
    }

    /**
     * Set params for creating a one time Payment
     *
     * @return array
     */
    private function prepareOneTimeData()
    {
        $customer = $this->model->getCustomer();
        $accountId = $customer->getPrimaryType() === self::CREDIT_CARD ? $customer->getCcId() : $customer->getBaId();
        $date = $this->model->getStartDate();

        $params = [
            'AccountId'              => $accountId,
            'PaymentAmount'          => $this->model->getAmount(),
            'StartDate'              => $date->format('Y-m-d'),
            'EndDate'                => $date->modify('+ 1 day')->format('Y-m-d'),
            'ExecutionFrequencyType' => 9
        ];

        return $params;
    }
}
