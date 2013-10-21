<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdBenefit
 */
class StdBenefit
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
    private $BenefitTraining;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->BenefitTraining = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdBenefit
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
     * Add BenefitTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $benefitTraining
     * @return StdBenefit
     */
    public function addBenefitTraining(\SO\TrainingBundle\Entity\Training $benefitTraining)
    {
        $this->BenefitTraining[] = $benefitTraining;
    
        return $this;
    }

    /**
     * Remove BenefitTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $benefitTraining
     */
    public function removeBenefitTraining(\SO\TrainingBundle\Entity\Training $benefitTraining)
    {
        $this->BenefitTraining->removeElement($benefitTraining);
    }

    /**
     * Get BenefitTraining
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBenefitTraining()
    {
        return $this->BenefitTraining;
    }
}