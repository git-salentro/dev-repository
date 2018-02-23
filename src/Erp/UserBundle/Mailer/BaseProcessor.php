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
     * @param $renderedTemplate
     * @param $fromEmail
     * @param $toEmail
     * @return int
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = $renderedLines[0];
        $body = implode("\n", array_slice($renderedLines, 1));

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);

        return $this->mailer->send($message);
    }
}