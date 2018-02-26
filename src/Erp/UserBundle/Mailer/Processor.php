<?php

namespace Erp\UserBundle\Mailer;

use Erp\UserBundle\Entity\Charge;

class Processor extends BaseProcessor
{
    CONST CHARGE_EMAIL_TEMPLATE = 'ErpUserBundle:Landlords:charge_email_template.html.twig';

    public function sendChargeEmail(Charge $charge)
    {
        $rendered = $this->templating->render(self::CHARGE_EMAIL_TEMPLATE, [
            'user' => $charge->getLandlord(),
            'charge' => $charge,
        ]);

        // TODO From Email retrieve from db
        return $this->sendEmail($rendered, 'Confirm Charge', 'support@zoobdoo.com', $charge->getLandlord()->getEmail());
    }
}