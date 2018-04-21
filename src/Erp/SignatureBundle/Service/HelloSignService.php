<?php

namespace Erp\SignatureBundle\Service;

use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\CoreBundle\Entity\Document;

class HelloSignService {

    const HELLOSIGN_STATUS_SENT = 'sent';
    const HELLOSIGN_STATUS_CREATED = 'created';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Construct method
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * 
     * @param Document $document
     * @param User $sender
     * @param User $recipient
     * @param string $subject
     * @param string $message
     * @return type
     * @throws \Exception
     */
    public function embedSignatureRequestFromDocument(Document $document, User $sender, User $recipient, $subject = '', $message = '') {
        try {
            $client = $this->container->get('hellosign.client');

            $pathFileToSign = $document->getUploadBaseDir($this->container) . $document->getPath() . '/' . $document->getName();
            
            // $request = new HelloSign\TemplateSignatureRequest;
            $request = new \HelloSign\SignatureRequest;

            $request->enableTestMode();
            // $request->setTemplateId('b0ee832977d76cc3240364e0287ccfd1544bb454');
            $request->setSubject($subject);
            $request->setMessage($message);
            // $request->addSigner($sender->getEmail(), $sender->getFullName());
            $request->addSigner($recipient->getEmail(), $recipient->getFullName());
            $request->addFile($pathFileToSign);
            
            $embedded_request = new \HelloSign\EmbeddedSignatureRequest($request, $this->container->getParameter('hellosign_app_clientid'));
            $response = $client->createEmbeddedSignatureRequest($embedded_request);

            return $response;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}
