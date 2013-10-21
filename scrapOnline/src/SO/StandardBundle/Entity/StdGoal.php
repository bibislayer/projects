<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdGoal
 */
class StdGoal
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
    private $GoalTraining;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->GoalTraining = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdGoal
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
     * Add GoalTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $goalTraining
     * @return StdGoal
     */
    public function addGoalTraining(\SO\TrainingBundle\Entity\Training $goalTraining)
    {
        $this->GoalTraining[] = $goalTraining;
    
        return $this;
    }

    /**
     * Remove GoalTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $goalTraining
     */
    public function removeGoalTraining(\SO\TrainingBundle\Entity\Training $goalTraining)
    {
        $this->GoalTraining->removeElement($goalTraining);
    }

    /**
     * Get GoalTraining
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGoalTraining()
    {
        return $this->GoalTraining;
    }
}