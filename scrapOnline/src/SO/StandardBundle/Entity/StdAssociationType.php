<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdAssociationType
 */
class StdAssociationType
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
    private $slug;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Association;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Association = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdAssociationType
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
     * Set slug
     *
     * @param string $slug
     * @return StdAssociationType
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
     * Add Association
     *
     * @param \SO\AssociationBundle\Entity\Association $association
     * @return StdAssociationType
     */
    public function addAssociation(\SO\AssociationBundle\Entity\Association $association)
    {
        $this->Association[] = $association;
    
        return $this;
    }

    /**
     * Remove Association
     *
     * @param \SO\AssociationBundle\Entity\Association $association
     */
    public function removeAssociation(\SO\AssociationBundle\Entity\Association $association)
    {
        $this->Association->removeElement($association);
    }

    /**
     * Get Association
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssociation()
    {
        return $this->Association;
    }
}