<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdDiplomaType
 */
class StdDiplomaType
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
    private $other_name;

    /**
     * @var integer
     */
    private $period;

    /**
     * @var string
     */
    private $period_type;

    /**
     * @var integer
     */
    private $demand;

    /**
     * @var string
     */
    private $text_introduction;

    /**
     * @var string
     */
    private $text_presentation;

    /**
     * @var string
     */
    private $text_training;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var integer
     */
    private $approbation;

    /**
     * @var integer
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
     * @var \SO\StandardBundle\Entity\StdLevelStudy
     */
    private $StartLevel;

    /**
     * @var \SO\StandardBundle\Entity\StdLevelStudy
     */
    private $FinalLevel;

    /**
     * @var \SO\StandardBundle\Entity\StdDiplomaNature
     */
    private $DiplomaNature;

    /**
     * @var \SO\StandardBundle\Entity\StdDiplomaLevel
     */
    private $DiplomaEtat;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $DiplomaTypeGoal;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $DiplomaTypeBenefit;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->DiplomaTypeGoal = new \Doctrine\Common\Collections\ArrayCollection();
        $this->DiplomaTypeBenefit = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdDiplomaType
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
     * Set other_name
     *
     * @param string $otherName
     * @return StdDiplomaType
     */
    public function setOtherName($otherName)
    {
        $this->other_name = $otherName;
    
        return $this;
    }

    /**
     * Get other_name
     *
     * @return string 
     */
    public function getOtherName()
    {
        return $this->other_name;
    }

    /**
     * Set period
     *
     * @param integer $period
     * @return StdDiplomaType
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    
        return $this;
    }

    /**
     * Get period
     *
     * @return integer 
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set period_type
     *
     * @param string $periodType
     * @return StdDiplomaType
     */
    public function setPeriodType($periodType)
    {
        $this->period_type = $periodType;
    
        return $this;
    }

    /**
     * Get period_type
     *
     * @return string 
     */
    public function getPeriodType()
    {
        return $this->period_type;
    }

    /**
     * Set demand
     *
     * @param integer $demand
     * @return StdDiplomaType
     */
    public function setDemand($demand)
    {
        $this->demand = $demand;
    
        return $this;
    }

    /**
     * Get demand
     *
     * @return integer 
     */
    public function getDemand()
    {
        return $this->demand;
    }

    /**
     * Set text_introduction
     *
     * @param string $textIntroduction
     * @return StdDiplomaType
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
     * Set text_presentation
     *
     * @param string $textPresentation
     * @return StdDiplomaType
     */
    public function setTextPresentation($textPresentation)
    {
        $this->text_presentation = $textPresentation;
    
        return $this;
    }

    /**
     * Get text_presentation
     *
     * @return string 
     */
    public function getTextPresentation()
    {
        return $this->text_presentation;
    }

    /**
     * Set text_training
     *
     * @param string $textTraining
     * @return StdDiplomaType
     */
    public function setTextTraining($textTraining)
    {
        $this->text_training = $textTraining;
    
        return $this;
    }

    /**
     * Get text_training
     *
     * @return string 
     */
    public function getTextTraining()
    {
        return $this->text_training;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return StdDiplomaType
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
     * @param integer $approbation
     * @return StdDiplomaType
     */
    public function setApprobation($approbation)
    {
        $this->approbation = $approbation;
    
        return $this;
    }

    /**
     * Get approbation
     *
     * @return integer 
     */
    public function getApprobation()
    {
        return $this->approbation;
    }

    /**
     * Set published
     *
     * @param integer $published
     * @return StdDiplomaType
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return integer 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set published_at
     *
     * @param \DateTime $publishedAt
     * @return StdDiplomaType
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
     * @return StdDiplomaType
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
     * @return StdDiplomaType
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
     * @return StdDiplomaType
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
     * Set StartLevel
     *
     * @param \SO\StandardBundle\Entity\StdLevelStudy $startLevel
     * @return StdDiplomaType
     */
    public function setStartLevel(\SO\StandardBundle\Entity\StdLevelStudy $startLevel = null)
    {
        $this->StartLevel = $startLevel;
    
        return $this;
    }

    /**
     * Get StartLevel
     *
     * @return \SO\StandardBundle\Entity\StdLevelStudy 
     */
    public function getStartLevel()
    {
        return $this->StartLevel;
    }

    /**
     * Set FinalLevel
     *
     * @param \SO\StandardBundle\Entity\StdLevelStudy $finalLevel
     * @return StdDiplomaType
     */
    public function setFinalLevel(\SO\StandardBundle\Entity\StdLevelStudy $finalLevel = null)
    {
        $this->FinalLevel = $finalLevel;
    
        return $this;
    }

    /**
     * Get FinalLevel
     *
     * @return \SO\StandardBundle\Entity\StdLevelStudy 
     */
    public function getFinalLevel()
    {
        return $this->FinalLevel;
    }

    /**
     * Set DiplomaNature
     *
     * @param \SO\StandardBundle\Entity\StdDiplomaNature $diplomaNature
     * @return StdDiplomaType
     */
    public function setDiplomaNature(\SO\StandardBundle\Entity\StdDiplomaNature $diplomaNature = null)
    {
        $this->DiplomaNature = $diplomaNature;
    
        return $this;
    }

    /**
     * Get DiplomaNature
     *
     * @return \SO\StandardBundle\Entity\StdDiplomaNature 
     */
    public function getDiplomaNature()
    {
        return $this->DiplomaNature;
    }

    /**
     * Set DiplomaEtat
     *
     * @param \SO\StandardBundle\Entity\StdDiplomaLevel $diplomaEtat
     * @return StdDiplomaType
     */
    public function setDiplomaEtat(\SO\StandardBundle\Entity\StdDiplomaLevel $diplomaEtat = null)
    {
        $this->DiplomaEtat = $diplomaEtat;
    
        return $this;
    }

    /**
     * Get DiplomaEtat
     *
     * @return \SO\StandardBundle\Entity\StdDiplomaLevel 
     */
    public function getDiplomaEtat()
    {
        return $this->DiplomaEtat;
    }

    /**
     * Add DiplomaTypeGoal
     *
     * @param \SO\StandardBundle\Entity\StdGoal $diplomaTypeGoal
     * @return StdDiplomaType
     */
    public function addDiplomaTypeGoal(\SO\StandardBundle\Entity\StdGoal $diplomaTypeGoal)
    {
        $this->DiplomaTypeGoal[] = $diplomaTypeGoal;
    
        return $this;
    }

    /**
     * Remove DiplomaTypeGoal
     *
     * @param \SO\StandardBundle\Entity\StdGoal $diplomaTypeGoal
     */
    public function removeDiplomaTypeGoal(\SO\StandardBundle\Entity\StdGoal $diplomaTypeGoal)
    {
        $this->DiplomaTypeGoal->removeElement($diplomaTypeGoal);
    }

    /**
     * Get DiplomaTypeGoal
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDiplomaTypeGoal()
    {
        return $this->DiplomaTypeGoal;
    }

    /**
     * Add DiplomaTypeBenefit
     *
     * @param \SO\StandardBundle\Entity\StdBenefit $diplomaTypeBenefit
     * @return StdDiplomaType
     */
    public function addDiplomaTypeBenefit(\SO\StandardBundle\Entity\StdBenefit $diplomaTypeBenefit)
    {
        $this->DiplomaTypeBenefit[] = $diplomaTypeBenefit;
    
        return $this;
    }

    /**
     * Remove DiplomaTypeBenefit
     *
     * @param \SO\StandardBundle\Entity\StdBenefit $diplomaTypeBenefit
     */
    public function removeDiplomaTypeBenefit(\SO\StandardBundle\Entity\StdBenefit $diplomaTypeBenefit)
    {
        $this->DiplomaTypeBenefit->removeElement($diplomaTypeBenefit);
    }

    /**
     * Get DiplomaTypeBenefit
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDiplomaTypeBenefit()
    {
        return $this->DiplomaTypeBenefit;
    }
}