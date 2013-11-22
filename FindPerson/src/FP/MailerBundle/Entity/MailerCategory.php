<?php

namespace FP\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailerCategory
 */
class MailerCategory
{
   
 
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $lft;

    /**
     * @var integer
     */
    private $rgt;

    /**
     * @var integer
     */
    private $root_id;

    /**
     * @var integer
     */
    private $level;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Mailer;

    /**
     * @var \FP\MailerBundle\Entity\MailerCategory
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Mailer = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return MailerCategory
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     * @return MailerCategory
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    
        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return MailerCategory
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    
        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root_id
     *
     * @param integer $rootId
     * @return MailerCategory
     */
    public function setRootId($rootId)
    {
        $this->root_id = $rootId;
    
        return $this;
    }

    /**
     * Get root_id
     *
     * @return integer 
     */
    public function getRootId()
    {
        return $this->root_id;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return MailerCategory
     */
    public function setLevel($level)
    {
        $this->level = $level;
    
        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return MailerCategory
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return MailerCategory
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Add children
     *
     * @param \FP\MailerBundle\Entity\MailerCategory $children
     * @return MailerCategory
     */
    public function addChildren(\FP\MailerBundle\Entity\MailerCategory $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \FP\MailerBundle\Entity\MailerCategory $children
     */
    public function removeChildren(\FP\MailerBundle\Entity\MailerCategory $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add Mailer
     *
     * @param \FP\MailerBundle\Entity\Mailer $mailer
     * @return MailerCategory
     */
    public function addMailer(\FP\MailerBundle\Entity\Mailer $mailer)
    {
        $this->Mailer[] = $mailer;
    
        return $this;
    }

    /**
     * Remove Mailer
     *
     * @param \FP\MailerBundle\Entity\Mailer $mailer
     */
    public function removeMailer(\FP\MailerBundle\Entity\Mailer $mailer)
    {
        $this->Mailer->removeElement($mailer);
    }

    /**
     * Get Mailer
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMailer()
    {
        return $this->Mailer;
    }

    /**
     * Set parent
     *
     * @param \FP\MailerBundle\Entity\MailerCategory $parent
     * @return MailerCategory
     */
    public function setParent(\FP\MailerBundle\Entity\MailerCategory $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \FP\MailerBundle\Entity\MailerCategory 
     */
    public function getParent()
    {
        return $this->parent;
    }
}