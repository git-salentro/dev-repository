<?php

namespace Erp\UserBundle\Mailer;

use Erp\UserBundle\Entity\Charge;

class Processor extends BaseProcessor
{
    CONST CHARGE_EMAIL_TEMPLATE = 'ErpUserBundle:Landlords:charge_email_template.html.twig';

    public function sendChargeEmail(Charge $charge)
    {
        $rendered = $this->templating->render(self::CHARGE_EMAIL_TEMPLATE, ['charge' => $charge]);
        $subject = sprintf('Charge request from %s to %s', $charge->getManager()->getFullName(), $charge->getLandlord()->getFullName());
        $result = $this->sendEmail($rendered, $subject, 'support@zoobdoo.com', $charge->getLandlord()->getEmail(), 'text/html');

        return $result;
    }
}