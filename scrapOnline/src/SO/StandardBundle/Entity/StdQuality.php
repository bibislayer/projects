<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdQuality
 */
class StdQuality
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QualityProfession;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QualityTraining;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->QualityProfession = new \Doctrine\Common\Collections\ArrayCollection();
        $this->QualityTraining = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdQuality
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
     * Add QualityProfession
     *
     * @param \SO\ProfessionBundle\Entity\Profession $qualityProfession
     * @return StdQuality
     */
    public function addQualityProfession(\SO\ProfessionBundle\Entity\Profession $qualityProfession)
    {
        $this->QualityProfession[] = $qualityProfession;
    
        return $this;
    }

    /**
     * Remove QualityProfession
     *
     * @param \SO\ProfessionBundle\Entity\Profession $qualityProfession
     */
    public function removeQualityProfession(\SO\ProfessionBundle\Entity\Profession $qualityProfession)
    {
        $this->QualityProfession->removeElement($qualityProfession);
    }

    /**
     * Get QualityProfession
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQualityProfession()
    {
        return $this->QualityProfession;
    }

    /**
     * Add QualityTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $qualityTraining
     * @return StdQuality
     */
    public function addQualityTraining(\SO\TrainingBundle\Entity\Training $qualityTraining)
    {
        $this->QualityTraining[] = $qualityTraining;
    
        return $this;
    }

    /**
     * Remove QualityTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $qualityTraining
     */
    public function removeQualityTraining(\SO\TrainingBundle\Entity\Training $qualityTraining)
    {
        $this->QualityTraining->removeElement($qualityTraining);
    }

    /**
     * Get QualityTraining
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQualityTraining()
    {
        return $this->QualityTraining;
    }
}