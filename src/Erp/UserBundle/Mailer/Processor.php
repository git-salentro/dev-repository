<?php

namespace Erp\UserBundle\Mailer;

use Erp\UserBundle\Entity\Charge;
use Erp\UserBundle\Entity\User;

class Processor extends BaseProcessor
{
    const CHARGE_EMAIL_TEMPLATE = 'ErpUserBundle:Landlords:charge_email_template.html.twig';
    const END_OF_TRIAL_PERIOD_TEMPLATE = 'ErpUserBundle:Landlords:end_of_trial_period_template.html.twig';

    public function sendChargeEmail(Charge $charge, $mailFrom)
    {
        $rendered = $this->templating->render(self::CHARGE_EMAIL_TEMPLATE, ['charge' => $charge]);
        $subject = sprintf('Charge request from %s to %s', $charge->getManager()->getFullName(), $charge->getLandlord()->getFullName());
        $result = $this->sendEmail($rendered, $subject, $mailFrom, $charge->getLandlord()->getEmail(), 'text/html');

        return $result;
    }

    public function sendEndOfTrialPeriodEmail(User $user, $mailFrom)
    {
        $rendered = $this->templating->render(self::END_OF_TRIAL_PERIOD_TEMPLATE, ['user' => $user]);
        $subject = sprintf('Subject');
        $result = $this->sendEmail($rendered, $subject, $mailFrom, $user->getEmail(), 'text/html');

        return $result;
    }
}