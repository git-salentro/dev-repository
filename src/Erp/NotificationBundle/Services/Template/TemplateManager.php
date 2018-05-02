<?php

namespace Erp\NotificationBundle\Services\Template;

use Erp\NotificationBundle\Entity\Template;
use Erp\NotificationBundle\Repository\TemplateRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\TwigBundle\TwigEngine;

class TemplateManager
{
    const EMAIL_TEMPLATE = 'ErpNotificationBundle:Template:mail.html.twig';

    private $twig;
    private $repo;

    public function __construct(TwigEngine $twig, TemplateRepository $repo)
    {
        $this->twig = $twig;
        $this->repo = $repo;
    }

    public function renderTemplateById(int $id)
    {
        if ($template = $this->repo->find($id)) {
            return $this->renderTemplate($template);
        }
        throw new NotFoundHttpException('Template with id '.$id.' not found');
    }

    public function renderTemplate(Template $template)
    {
        $parameters = [
            'content' => $template->getDescription(),
        ];
        return $this->twig->render(self::EMAIL_TEMPLATE, $parameters);
    }
}
