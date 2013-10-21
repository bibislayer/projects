<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdSkill
 */
class StdSkill
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
    private $SkillProfession;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $SkillTraining;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->SkillProfession = new \Doctrine\Common\Collections\ArrayCollection();
        $this->SkillTraining = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdSkill
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
     * Add SkillProfession
     *
     * @param \SO\ProfessionBundle\Entity\Profession $skillProfession
     * @return StdSkill
     */
    public function addSkillProfession(\SO\ProfessionBundle\Entity\Profession $skillProfession)
    {
        $this->SkillProfession[] = $skillProfession;
    
        return $this;
    }

    /**
     * Remove SkillProfession
     *
     * @param \SO\ProfessionBundle\Entity\Profession $skillProfession
     */
    public function removeSkillProfession(\SO\ProfessionBundle\Entity\Profession $skillProfession)
    {
        $this->SkillProfession->removeElement($skillProfession);
    }

    /**
     * Get SkillProfession
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSkillProfession()
    {
        return $this->SkillProfession;
    }

    /**
     * Add SkillTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $skillTraining
     * @return StdSkill
     */
    public function addSkillTraining(\SO\TrainingBundle\Entity\Training $skillTraining)
    {
        $this->SkillTraining[] = $skillTraining;
    
        return $this;
    }

    /**
     * Remove SkillTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $skillTraining
     */
    public function removeSkillTraining(\SO\TrainingBundle\Entity\Training $skillTraining)
    {
        $this->SkillTraining->removeElement($skillTraining);
    }

    /**
     * Get SkillTraining
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSkillTraining()
    {
        return $this->SkillTraining;
    }
}