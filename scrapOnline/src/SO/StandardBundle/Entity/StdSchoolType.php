<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdSchoolType
 */
class StdSchoolType
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
     * @var string
     */
    private $text_introduction;

    /**
     * @var string
     */
    private $text_description;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var boolean
     */
    private $approbation;

    /**
     * @var boolean
     */
    private $published;

    /**
     * @var \DateTime
     */
    private $published_at;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $School;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->School = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdSchoolType
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
     * Set text_introduction
     *
     * @param string $textIntroduction
     * @return StdSchoolType
     */
    public function setTextIntroduction($textIntroduction)
    {
        $this->text_introduction = $textIntroduction;
    
        return $this;
    }

    /**
     * Get text_introduction
     *
     * @return string 
     */
    public function getTextIntroduction()
    {
        return $this->text_introduction;
    }

    /**
     * Set text_description
     *
     * @param string $textDescription
     * @return StdSchoolType
     */
    public function setTextDescription($textDescription)
    {
        $this->text_description = $textDescription;
    
        return $this;
    }

    /**
     * Get text_description
     *
     * @return string 
     */
    public function getTextDescription()
    {
        return $this->text_description;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return StdSchoolType
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set approbation
     *
     * @param boolean $approbation
     * @return StdSchoolType
     */
    public function setApprobation($approbation)
    {
        $this->approbation = $approbation;
    
        return $this;
    }

    /**
     * Get approbation
     *
     * @return boolean 
     */
    public function getApprobation()
    {
        return $this->approbation;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return StdSchoolType
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set published_at
     *
     * @param \DateTime $publishedAt
     * @return StdSchoolType
     */
    public function setPublishedAt($publishedAt)
    {
        $this->published_at = $publishedAt;
    
        return $this;
    }

    /**
     * Get published_at
     *
     * @return \DateTime 
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return StdSchoolType
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
     * @return StdSchoolType
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
     * Set slug
     *
     * @param string $slug
     * @return StdSchoolType
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add School
     *
     * @param \SO\SchoolBundle\Entity\School $school
     * @return StdSchoolType
     */
    public function addSchool(\SO\SchoolBundle\Entity\School $school)
    {
        $this->School[] = $school;
    
        return $this;
    }

    /**
     * Remove School
     *
     * @param \SO\SchoolBundle\Entity\School $school
     */
    public function removeSchool(\SO\SchoolBundle\Entity\School $school)
    {
        $this->School->removeElement($school);
    }

    /**
     * Get School
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSchool()
    {
        return $this->School;
    }
}