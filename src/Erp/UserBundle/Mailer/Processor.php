<?php

namespace Erp\UserBundle\Mailer;

use Erp\UserBundle\Entity\Charge;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;

class Processor extends BaseProcessor
{
    CONST CHARGE_EMAIL_TEMPLATE = 'ErpUserBundle:Landlords:charge_email_template.html.twig';

    public function sendChargeEmail(Charge $charge)
    {
        $rendered = $this->templating->render(self::CHARGE_EMAIL_TEMPLATE, array(
            'user' => $charge->getLandlord(),
            'charge' => $charge,
        ));

        return $this->sendEmailMessage($rendered, 'support@zoobdoo.com', $charge->getLandlord()->getEmail());
    }
}