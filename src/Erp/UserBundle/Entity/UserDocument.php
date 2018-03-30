<?php

namespace Erp\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\CoreBundle\Entity\Document;
use Erp\UserBundle\Entity\User;

/**
 * UserDocumentEntity
 *
 * @ORM\Table(name="user_documents")
 * @ORM\Entity(repositoryClass="Erp\UserBundle\Repository\UserDocumentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserDocument
{
    const STATUS_SENT = 'Sent';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_PENDING = 'Pending';
    const APPLICANT_USER_ID = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User",
     *      inversedBy="userDocuments"
     * )
     * @ORM\JoinColumn(
     *      name="from_user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $fromUser;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="\Erp\UserBundle\Entity\User"
     * )
     * @ORM\JoinColumn(
     *      name="to_user_id",
     *      referencedColumnName="id",
     *      onDelete="CASCADE"
     * )
     */
    protected $toUser;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="status",
     *      type="string",
     *      columnDefinition="ENUM('Sent','Completed','Pending') DEFAULT 'Pending'"
     * )
     */
    protected $status = self::STATUS_PENDING;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    protected $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime")
     */
    protected $updatedDate;

    /**
     * @ORM\OneToOne(
     *      targetEntity="\Erp\CoreBundle\Entity\Document",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     *
     * @ORM\JoinColumn(
     *      name="document_id",
     *      referencedColumnName="id"
     * )
     */
    protected $document;

    /**
     * @var string
     *
     * @ORM\Column(name="envelop_id", type="string", nullable=true)
     */
    protected $envelopId;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return UserDocument
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     */
    public function setCreatedDate()
    {
        $this->createdDate = new \DateTime();
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedDate()
    {
        $this->updatedDate = new \DateTime();
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set fromUser
     *
     * @param User|null $fromUser
     *
     * @return UserDocument
     */
    public function setFromUser($fromUser)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get fromUser
     *
     * @return User
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set toUser
     *
     * @param User $toUser
     *
     * @return UserDocument
     */
    public function setToUser(User $toUser)
    {
        $this->toUser = $toUser;

        return $this;
    }

    /**
     * Get toUser
     *
     * @return User
     */
    public function getToUser()
    {
        return $this->toUser;
    }

    /**
     * Set document
     *
     * @param Document $document
     *
     * @return UserDocument
     */
    public function setDocument(Document $document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set envelopId
     *
     * @param string $envelopId
     *
     * @return UserDocument
     */
    public function setEnvelopId($envelopId)
    {
        $this->envelopId = $envelopId;

        return $this;
    }

    /**
     * Get envelopId
     *
     * @return string
     */
    public function getEnvelopId()
    {
        return $this->envelopId;
    }

    public function isSigned()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isSent()
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }
}
