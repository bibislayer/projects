<?php

namespace VM\VideoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 */
class Video
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
    private $text_description;

    /**
     * @var integer
     */
    private $position;

    /**
     * @var integer
     */
    private $time_limit;

    /**
     * @var array
     */
    private $tag;

    /**
     * @var string
     */
    private $enclosed_files;

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
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \VM\UserBundle\Entity\Userr
     */
    private $User;

    /**
     * @var \VM\VideoBundle\Entity\Video
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
     * @return Video
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
     * Set text_description
     *
     * @param string $textDescription
     * @return Video
     */
    public function setTextDescription($textDescription)
    {
        $this->text_description = $textDescription;
    
        return $this;
    }

    /**
     * Get text_description
     *
     * @return string 
     */
    public function getTextDescription()
    {
        return $this->text_description;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Video
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set time_limit
     *
     * @param integer $timeLimit
     * @return Video
     */
    public function setTimeLimit($timeLimit)
    {
        $this->time_limit = $timeLimit;
    
        return $this;
    }

    /**
     * Get time_limit
     *
     * @return integer 
     */
    public function getTimeLimit()
    {
        return $this->time_limit;
    }

    /**
     * Set tag
     *
     * @param array $tag
     * @return Video
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    
        return $this;
    }

    /**
     * Get tag
     *
     * @return array 
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set enclosed_files
     *
     * @param string $enclosedFiles
     * @return Video
     */
    public function setEnclosedFiles($enclosedFiles)
    {
        $this->enclosed_files = $enclosedFiles;
    
        return $this;
    }

    /**
     * Get enclosed_files
     *
     * @return string 
     */
    public function getEnclosedFiles()
    {
        return $this->enclosed_files;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     * @return Video
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
     * @return Video
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
     * @return Video
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
     * @return Video
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Video
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Video
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Video
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
     * @param \VM\VideoBundle\Entity\Video $children
     * @return Video
     */
    public function addChildren(\VM\VideoBundle\Entity\Video $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \VM\VideoBundle\Entity\Video $children
     */
    public function removeChildren(\VM\VideoBundle\Entity\Video $children)
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
     * Set User
     *
     * @param \VM\UserBundle\Entity\Userr $user
     * @return Video
     */
    public function setUser(\VM\UserBundle\Entity\Userr $user = null)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get User
     *
     * @return \VM\UserBundle\Entity\Userr 
     */
    public function getUser()
    {
        return $this->User;
    }

    /**
     * Set parent
     *
     * @param \VM\VideoBundle\Entity\Video $parent
     * @return Video
     */
    public function setParent(\VM\VideoBundle\Entity\Video $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \VM\VideoBundle\Entity\Video 
     */
    public function getParent()
    {
        return $this->parent;
    }
}