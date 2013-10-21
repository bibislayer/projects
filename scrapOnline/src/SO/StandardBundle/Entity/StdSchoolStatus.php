<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdSchoolStatus
 */
class StdSchoolStatus
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
     * @return StdSchoolStatus
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
     * Add School
     *
     * @param \SO\SchoolBundle\Entity\School $school
     * @return StdSchoolStatus
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