<?php

namespace FP\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdCategory
 */
class StdCategory
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
    private $introduction;

    /**
     * @var string
     */
    private $presentation;

    /**
     * @var integer
     */
    private $approbation;

    /**
     * @var integer
     */
    private $published;

    /**
     * @var \DateTime
     */
    private $published_at;

    /**
     * @var integer
     */
    private $lft;

    /**
     * @var integer
     */
    private $rgt;

    /**
     * @var integer
     */
    private $root_id;

    /**
     * @var integer
     */
    private $level;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \FP\StandardBundle\Entity\StdCategory
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdCategory
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
     * Set introduction
     *
     * @param string $introduction
     * @return StdCategory
     */
    public function setIntroduction($introduction)
    {
        $this->introduction = $introduction;
    
        return $this;
    }

    /**
     * Get introduction
     *
     * @return string 
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     * Set presentation
     *
     * @param string $presentation
     * @return StdCategory
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;
    
        return $this;
    }

    /**
     * Get presentation
     *
     * @return string 
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set approbation
     *
     * @param integer $approbation
     * @return StdCategory
     */
    public function setApprobation($approbation)
    {
        $this->approbation = $approbation;
    
        return $this;
    }

    /**
     * Get approbation
     *
     * @return integer 
     */
    public function getApprobation()
    {
        return $this->approbation;
    }

    /**
     * Set published
     *
     * @param integer $published
     * @return StdCategory
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return integer 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set published_at
     *
     * @param \DateTime $publishedAt
     * @return StdCategory
     */
    public function setPublishedAt($publishedAt)
    {
        $this->published_at = $publishedAt;
    
        return $this;
    }

    /**
     * Get published_at
     *
     * @return \DateTime 
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     * @return StdCategory
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    
        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return StdCategory
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    
        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root_id
     *
     * @param integer $rootId
     * @return StdCategory
     */
    public function setRootId($rootId)
    {
        $this->root_id = $rootId;
    
        return $this;
    }

    /**
     * Get root_id
     *
     * @return integer 
     */
    public function getRootId()
    {
        return $this->root_id;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return StdCategory
     */
    public function setLevel($level)
    {
        $this->level = $level;
    
        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return StdCategory
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
     * Add children
     *
     * @param \FP\StandardBundle\Entity\StdCategory $children
     * @return StdCategory
     */
    public function addChildren(\FP\StandardBundle\Entity\StdCategory $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \FP\StandardBundle\Entity\StdCategory $children
     */
    public function removeChildren(\FP\StandardBundle\Entity\StdCategory $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set parent
     *
     * @param \FP\StandardBundle\Entity\StdCategory $parent
     * @return StdCategory
     */
    public function setParent(\FP\StandardBundle\Entity\StdCategory $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \FP\StandardBundle\Entity\StdCategory 
     */
    public function getParent()
    {
        return $this->parent;
    }
}