<?php

namespace Erp\PropertyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Erp\PropertyBundle\Entity\ApplicationSection;
use Erp\UserBundle\Entity\User;

/**
 * ApplicationForm
 *
 * @ORM\Table(name="application_forms")
 * @ORM\Entity(repositoryClass="Erp\PropertyBundle\Repository\ApplicationFormRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ApplicationForm
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_default", type="boolean")
     */
    protected $isDefault = false;

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
     *      targetEntity="\Erp\UserBundle\Entity\User",
     *      inversedBy="applicationForm"
     * )
     * @ORM\JoinColumn(
     *      name="user_id",
     *      referencedColumnName="id"
     * )
     */
    protected $user;

    /**
     * @ORM\OneToMany(
     *      targetEntity="\Erp\PropertyBundle\Entity\ApplicationSection",
     *      mappedBy="applicationForm",
     *      cascade={"persist"},
     *      orphanRemoval=true
     * )
     */
    protected $applicationSections;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applicationSections = new ArrayCollection();
    }

    /**
     * Clone
     */
    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $this->setIsDefault(false);

            $applicationSections = $this->getApplicationSections();

            if ($applicationSections) {
                foreach ($applicationSections as $applicationSection) {
                    /** @var ApplicationSection $applicationSectionClone */
                    $applicationSectionClone = clone $applicationSection;
                    $applicationSectionClone->setApplicationForm($this);

                    $applicationSections->add($applicationSectionClone);
                }
            }
        }
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return ApplicationSection
     */
    private function setId($id = null)
    {
        $this->id = $id;

        return $this;
    }

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
     * Set isDefault
     *
     * @param boolean $isDefault
     *
     * @return ApplicationForm
     */
    public function setIsDefault($isDefault = false)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
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
     *
     * @return ApplicationSection
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
     * Add applicationSection
     *
     * @param ApplicationSection $applicationSection
     *
     * @return ApplicationForm
     */
    public function addApplicationSection(ApplicationSection $applicationSection)
    {
        $this->applicationSections[] = $applicationSection;

        return $this;
    }

    /**
     * Remove applicationSection
     *
     * @param ApplicationSection $applicationSection
     */
    public function removeApplicationSection(ApplicationSection $applicationSection)
    {
        $this->applicationSections->removeElement($applicationSection);
    }

    /**
     * Get applicationSections
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApplicationSections()
    {
        return $this->applicationSections;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return ApplicationForm
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
