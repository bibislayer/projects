<?php

namespace SO\MovieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Movie
 */
class Movie
{
    /**
     * @var integer
     */
    private $code;

    /**
     * @var string
     */
    private $originalTitle;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $poster;

    /**
     * @var integer
     */
    private $productionYear;

    /**
     * @var float
     */
    private $pressRating;

    /**
     * @var float
     */
    private $publicRating;

    /**
     * @var string
     */
    private $keywords;

    /**
     * @var string
     */
    private $synopsis;

    /**
     * @var string
     */
    private $synopsisShort;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Link;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Genre;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Link = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Genre = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set code
     *
     * @param integer $code
     * @return Movie
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
     * Set originalTitle
     *
     * @param string $originalTitle
     * @return Movie
     */
    public function setOriginalTitle($originalTitle)
    {
        $this->originalTitle = $originalTitle;
    
        return $this;
    }

    /**
     * Get originalTitle
     *
     * @return string 
     */
    public function getOriginalTitle()
    {
        return $this->originalTitle;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Movie
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set poster
     *
     * @param string $poster
     * @return Movie
     */
    public function setPoster($poster)
    {
        $this->poster = $poster;
    
        return $this;
    }

    /**
     * Get poster
     *
     * @return string 
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * Set productionYear
     *
     * @param integer $productionYear
     * @return Movie
     */
    public function setProductionYear($productionYear)
    {
        $this->productionYear = $productionYear;
    
        return $this;
    }

    /**
     * Get productionYear
     *
     * @return integer 
     */
    public function getProductionYear()
    {
        return $this->productionYear;
    }

    /**
     * Set pressRating
     *
     * @param float $pressRating
     * @return Movie
     */
    public function setPressRating($pressRating)
    {
        $this->pressRating = $pressRating;
    
        return $this;
    }

    /**
     * Get pressRating
     *
     * @return float 
     */
    public function getPressRating()
    {
        return $this->pressRating;
    }

    /**
     * Set publicRating
     *
     * @param float $publicRating
     * @return Movie
     */
    public function setPublicRating($publicRating)
    {
        $this->publicRating = $publicRating;
    
        return $this;
    }

    /**
     * Get publicRating
     *
     * @return float 
     */
    public function getPublicRating()
    {
        return $this->publicRating;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Movie
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    
        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set synopsis
     *
     * @param string $synopsis
     * @return Movie
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    
        return $this;
    }

    /**
     * Get synopsis
     *
     * @return string 
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * Set synopsisShort
     *
     * @param string $synopsisShort
     * @return Movie
     */
    public function setSynopsisShort($synopsisShort)
    {
        $this->synopsisShort = $synopsisShort;
    
        return $this;
    }

    /**
     * Get synopsisShort
     *
     * @return string 
     */
    public function getSynopsisShort()
    {
        return $this->synopsisShort;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Movie
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
     * Add Link
     *
     * @param \SO\MovieBundle\Entity\Link $link
     * @return Movie
     */
    public function addLink(\SO\MovieBundle\Entity\Link $link)
    {
        $this->Link[] = $link;
    
        return $this;
    }

    /**
     * Remove Link
     *
     * @param \SO\MovieBundle\Entity\Link $link
     */
    public function removeLink(\SO\MovieBundle\Entity\Link $link)
    {
        $this->Link->removeElement($link);
    }

    /**
     * Get Link
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLink()
    {
        return $this->Link;
    }

    /**
     * Add Genre
     *
     * @param \SO\MovieBundle\Entity\Genre $genre
     * @return Movie
     */
    public function addGenre(\SO\MovieBundle\Entity\Genre $genre)
    {
        $this->Genre[] = $genre;
    
        return $this;
    }

    /**
     * Remove Genre
     *
     * @param \SO\MovieBundle\Entity\Genre $genre
     */
    public function removeGenre(\SO\MovieBundle\Entity\Genre $genre)
    {
        $this->Genre->removeElement($genre);
    }

    /**
     * Get Genre
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGenre()
    {
        return $this->Genre;
    }
    /**
     * @var integer
     */
    private $runtime;


    /**
     * Set runtime
     *
     * @param integer $runtime
     * @return Movie
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;
    
        return $this;
    }

    /**
     * Get runtime
     *
     * @return integer 
     */
    public function getRuntime()
    {
        return $this->runtime;
    }
}