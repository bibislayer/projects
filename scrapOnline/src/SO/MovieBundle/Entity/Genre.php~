<?php

namespace SO\MovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Genre
 */
class Genre
{
    /**
     * @var integer
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Movie;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Movie = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set code
     *
     * @param integer $code
     * @return Genre
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Genre
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
     * Add Movie
     *
     * @param \SO\MovieBundle\Entity\Movie $movie
     * @return Genre
     */
    public function addMovie(\SO\MovieBundle\Entity\Movie $movie)
    {
        $this->Movie[] = $movie;
    
        return $this;
    }

    /**
     * Remove Movie
     *
     * @param \SO\MovieBundle\Entity\Movie $movie
     */
    public function removeMovie(\SO\MovieBundle\Entity\Movie $movie)
    {
        $this->Movie->removeElement($movie);
    }

    /**
     * Get Movie
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovie()
    {
        return $this->Movie;
    }
}