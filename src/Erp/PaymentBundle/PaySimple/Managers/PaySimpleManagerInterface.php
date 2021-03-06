<?php

namespace Erp\PaymentBundle\PaySimple\Managers;

use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;
use Erp\PaymentBundle\PaySimple\Exeptions\PaySimpleManagerException;
use Erp\PaymentBundle\PaySimple\Exeptions\PaySimpleModelException;

interface PaySimpleManagerInterface
{
    const STATUS_OK                     = 'ok';
    const STATUS_ERROR                  = 'error';

    const CREDIT_CARD                   = 'cc';
    const BANK_ACCOUNT                  = 'ba';

    const TYPE_CUSTOMER                 = 'pay_simple_customer';
    const TYPE_PAYMENT                  = 'pay_simple_payment';
    const TYPE_RECURRING                = 'pay_simple_recurring';

    const METHOD_CUSTOMER_GET_LIST_CUSTOMERS = 'method_customer_get_list_customers';
    const METHOD_CUSTOMER_GET_ACTIVE_SCHEDULES = 'method_customer_get_active_schedules';
    const METHOD_CUSTOMER_CREATE        = 'method_customer_create';
    const METHOD_CUSTOMER_GET_INFO      = 'method_customer_get_info';
    const METHOD_CUSTOMER_DELETE        = 'method_customer_delete';

    const METHOD_PAYMENT_CREATE_CC      = 'method_payment_create_cc';
    const METHOD_PAYMENT_CREATE_BA      = 'method_payment_create_ba';
    const METHOD_PAYMENT_SET_DEFAULT_PAYMENT_ACCOUNT = 'method_payment_set_default_payment_account';
    const METHOD_PAYMENT_MAKE           = 'method_payment_make';
    const METHOD_PAYMENT_GET_DEFAULT_CC = 'method_payment_get_default_cc';
    const METHOD_PAYMENT_GET_DEFAULT_BA = 'method_payment_get_default_ba';

    const METHOD_RECURRING_LIST         = 'method_recurring_list';
    const METHOD_RECURRING_CREATE       = 'method_recurring_create';
    const METHOD_RECURRING_SUSPEND      = 'method_recurring_suspend';
    const METHOD_RECURRING_GET          = 'method_recurring_get';
    const METHOD_RECURRING_ONE_TIME     = 'method_payment_one_time';

    /**
     * @param PaySimpleModelInterface $model
     *
     * @return $this
     *
     * @throws PaySimpleModelException
     */
    public function setModel(PaySimpleModelInterface $model = null);

    /**
     * @return mixed
     *
     * @param string $method
     *
     * @throws PaySimpleManagerException
     * @throws PaySimpleModelException
     */
    public function proccess($method = '');
}
