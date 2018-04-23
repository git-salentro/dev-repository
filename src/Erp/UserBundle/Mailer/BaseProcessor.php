<?php

namespace Erp\UserBundle\Mailer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

abstract class BaseProcessor
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param $rendered
     * @param $subject
     * @param $fromEmail
     * @param $toEmail
     * @param $contextType
     * @return int
     */
    protected function sendEmail($rendered, $subject, $fromEmail, $toEmail, $contextType = 'text/plain')
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($rendered, $contextType);

        return $this->mailer->send($message);
    }
}
