<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdPlaceDepartment
 */
class StdPlaceDepartment
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
    private $code;

    /**
     * @var \SO\StandardBundle\Entity\StdPlaceRegion
     */
    private $Region;


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
     * @return StdPlaceDepartment
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
     * Set code
     *
     * @param string $code
     * @return StdPlaceDepartment
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set Region
     *
     * @param \SO\StandardBundle\Entity\StdPlaceRegion $region
     * @return StdPlaceDepartment
     */
    public function setRegion(\SO\StandardBundle\Entity\StdPlaceRegion $region = null)
    {
        $this->Region = $region;
    
        return $this;
    }

    /**
     * Get Region
     *
     * @return \SO\StandardBundle\Entity\StdPlaceRegion 
     */
    public function getRegion()
    {
        return $this->Region;
    }
}