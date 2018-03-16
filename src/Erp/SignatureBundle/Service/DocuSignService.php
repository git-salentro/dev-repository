<?php
namespace Erp\SignatureBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Erp\SignatureBundle\DocuSignClient\DocuSignClient;
use Erp\SignatureBundle\DocuSignClient\Service\DocuSignDocument;
use Erp\SignatureBundle\DocuSignClient\Service\DocuSignRecipient;
use Erp\SignatureBundle\DocuSignClient\Service\DocuSignRequestSignatureService;
use Erp\CoreBundle\Entity\Document;

class DocuSignService
{
    const DOCUSIGN_STATUS_SENT = 'sent';
    const DOCUSIGN_STATUS_CREATED = 'created';

    /**
     * @var object DocuSignRequestSignatureService
     */
    public $service;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Construct method
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Create and sent envelope
     *
     * @param Document $document
     * @param          $email
     *
     * @return mixed
     */
    public function createEnvelopeFromDocument(Document $document, $email)
    {
        $client = $this->container->get('erp.signature.docusign.client');
        if ($client->hasError()) {
            throw new NotFoundHttpException($client->getErrorMessage());
        }

        $this->service = new DocuSignRequestSignatureService($client);
        $baseDir = $document->getUploadBaseDir($this->container);
        $file = $baseDir . $document->getPath() . '/' . $document->getName();

        if (!file_exists($file)) {
            throw new NotFoundHttpException('File not found');
        }

        $documents = [new DocuSignDocument($document->getOriginalName(), 1, file_get_contents($file))];
        $signers = [new DocuSignRecipient(1, 1, $email, $email)];
        $eventNotifications = [];

        $response = $this->service->signature->createEnvelopeFromDocument(
            'The document to be signed',
            'The document to be signed',
            self::DOCUSIGN_STATUS_SENT,
            $documents,
            $signers,
            $eventNotifications
        );

        return $response;
    }


    public function editEnvelopeFromDocument(Document $document, $emails)
    {
        $client = $this->container->get('erp.signature.docusign.client');
        if ($client->hasError()) {
            throw new NotFoundHttpException($client->getErrorMessage());
        }

        $this->service = new DocuSignRequestSignatureService($client);
        $baseDir = $document->getUploadBaseDir($this->container);
        $file = $baseDir.$document->getPath().'/'.$document->getName();

        if (!file_exists($file)) {
            throw new NotFoundHttpException('File not found');
        }

        $documents = [new DocuSignDocument($document->getOriginalName(), 1, file_get_contents($file))];

        $signers = [];
        $i = 0;
        foreach ($emails as $email) {
            $z = ++$i;
            $signers[] = new DocuSignRecipient($z, $z, $email, $email);
        }

        $response = $this->service->signature->editEnvelopFromDocument(
            'The document to be signed',
            'The document to be signed',
            self::DOCUSIGN_STATUS_SENT,
            $documents,
            $signers
        );

        return $response->url;
    }
}
