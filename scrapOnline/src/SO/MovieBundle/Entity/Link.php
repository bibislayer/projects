<?php

namespace SO\MovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Link
 */
class Link
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $mixture;

    /**
     * @var string
     */
    private $purevid;

    /**
     * @var boolean
     */
    private $visible;

    /**
     * @var \SO\MovieBundle\Entity\Movie
     */
    private $Movie;


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
     * Set mixture
     *
     * @param string $mixture
     * @return Link
     */
    public function setMixture($mixture)
    {
        $this->mixture = $mixture;
    
        return $this;
    }

    /**
     * Get mixture
     *
     * @return string 
     */
    public function getMixture()
    {
        return $this->mixture;
    }

    /**
     * Set purevid
     *
     * @param string $purevid
     * @return Link
     */
    public function setPurevid($purevid)
    {
        $this->purevid = $purevid;
    
        return $this;
    }

    /**
     * Get purevid
     *
     * @return string 
     */
    public function getPurevid()
    {
        return $this->purevid;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return Link
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set Movie
     *
     * @param \SO\MovieBundle\Entity\Movie $movie
     * @return Link
     */
    public function setMovie(\SO\MovieBundle\Entity\Movie $movie = null)
    {
        $this->Movie = $movie;
    
        return $this;
    }

    /**
     * Get Movie
     *
     * @return \SO\MovieBundle\Entity\Movie 
     */
    public function getMovie()
    {
        return $this->Movie;
    }
}