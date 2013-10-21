<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdPlaceCity
 */
class StdPlaceCity
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
    private $zip_code;

    /**
     * @var string
     */
    private $latitude;

    /**
     * @var string
     */
    private $longitude;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \SO\StandardBundle\Entity\StdPlaceDepartment
     */
    private $Department;


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
     * @return StdPlaceCity
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
     * Set zip_code
     *
     * @param string $zipCode
     * @return StdPlaceCity
     */
    public function setZipCode($zipCode)
    {
        $this->zip_code = $zipCode;
    
        return $this;
    }

    /**
     * Get zip_code
     *
     * @return string 
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return StdPlaceCity
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return StdPlaceCity
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return StdPlaceCity
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
     * Set Department
     *
     * @param \SO\StandardBundle\Entity\StdPlaceDepartment $department
     * @return StdPlaceCity
     */
    public function setDepartment(\SO\StandardBundle\Entity\StdPlaceDepartment $department = null)
    {
        $this->Department = $department;
    
        return $this;
    }

    /**
     * Get Department
     *
     * @return \SO\StandardBundle\Entity\StdPlaceDepartment 
     */
    public function getDepartment()
    {
        return $this->Department;
    }
}