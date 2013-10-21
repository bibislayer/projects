<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdPlaceRegion
 */
class StdPlaceRegion
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
     * @var \SO\StandardBundle\Entity\StdPlaceCountry
     */
    private $Country;


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
     * @return StdPlaceRegion
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
     * Set Country
     *
     * @param \SO\StandardBundle\Entity\StdPlaceCountry $country
     * @return StdPlaceRegion
     */
    public function setCountry(\SO\StandardBundle\Entity\StdPlaceCountry $country = null)
    {
        $this->Country = $country;
    
        return $this;
    }

    /**
     * Get Country
     *
     * @return \SO\StandardBundle\Entity\StdPlaceCountry 
     */
    public function getCountry()
    {
        return $this->Country;
    }
}