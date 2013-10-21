<?php

namespace SO\StandardBundle\Entity;

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
     * @var integer
     */
    private $sector_turnover;

    /**
     * @var string
     */
    private $sector_turnover_year;

    /**
     * @var integer
     */
    private $sector_employment;

    /**
     * @var string
     */
    private $sector_employment_year;

    /**
     * @var integer
     */
    private $sector_enterprises;

    /**
     * @var string
     */
    private $sector_enterprises_year;

    /**
     * @var integer
     */
    private $demand;

    /**
     * @var string
     */
    private $secteur_croissance;

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
     * @var \SO\StandardBundle\Entity\StdCategory
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $CategoryProfession;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $CategoryTraining;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->CategoryProfession = new \Doctrine\Common\Collections\ArrayCollection();
        $this->CategoryTraining = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set sector_turnover
     *
     * @param integer $sectorTurnover
     * @return StdCategory
     */
    public function setSectorTurnover($sectorTurnover)
    {
        $this->sector_turnover = $sectorTurnover;
    
        return $this;
    }

    /**
     * Get sector_turnover
     *
     * @return integer 
     */
    public function getSectorTurnover()
    {
        return $this->sector_turnover;
    }

    /**
     * Set sector_turnover_year
     *
     * @param string $sectorTurnoverYear
     * @return StdCategory
     */
    public function setSectorTurnoverYear($sectorTurnoverYear)
    {
        $this->sector_turnover_year = $sectorTurnoverYear;
    
        return $this;
    }

    /**
     * Get sector_turnover_year
     *
     * @return string 
     */
    public function getSectorTurnoverYear()
    {
        return $this->sector_turnover_year;
    }

    /**
     * Set sector_employment
     *
     * @param integer $sectorEmployment
     * @return StdCategory
     */
    public function setSectorEmployment($sectorEmployment)
    {
        $this->sector_employment = $sectorEmployment;
    
        return $this;
    }

    /**
     * Get sector_employment
     *
     * @return integer 
     */
    public function getSectorEmployment()
    {
        return $this->sector_employment;
    }

    /**
     * Set sector_employment_year
     *
     * @param string $sectorEmploymentYear
     * @return StdCategory
     */
    public function setSectorEmploymentYear($sectorEmploymentYear)
    {
        $this->sector_employment_year = $sectorEmploymentYear;
    
        return $this;
    }

    /**
     * Get sector_employment_year
     *
     * @return string 
     */
    public function getSectorEmploymentYear()
    {
        return $this->sector_employment_year;
    }

    /**
     * Set sector_enterprises
     *
     * @param integer $sectorEnterprises
     * @return StdCategory
     */
    public function setSectorEnterprises($sectorEnterprises)
    {
        $this->sector_enterprises = $sectorEnterprises;
    
        return $this;
    }

    /**
     * Get sector_enterprises
     *
     * @return integer 
     */
    public function getSectorEnterprises()
    {
        return $this->sector_enterprises;
    }

    /**
     * Set sector_enterprises_year
     *
     * @param string $sectorEnterprisesYear
     * @return StdCategory
     */
    public function setSectorEnterprisesYear($sectorEnterprisesYear)
    {
        $this->sector_enterprises_year = $sectorEnterprisesYear;
    
        return $this;
    }

    /**
     * Get sector_enterprises_year
     *
     * @return string 
     */
    public function getSectorEnterprisesYear()
    {
        return $this->sector_enterprises_year;
    }

    /**
     * Set demand
     *
     * @param integer $demand
     * @return StdCategory
     */
    public function setDemand($demand)
    {
        $this->demand = $demand;
    
        return $this;
    }

    /**
     * Get demand
     *
     * @return integer 
     */
    public function getDemand()
    {
        return $this->demand;
    }

    /**
     * Set secteur_croissance
     *
     * @param string $secteurCroissance
     * @return StdCategory
     */
    public function setSecteurCroissance($secteurCroissance)
    {
        $this->secteur_croissance = $secteurCroissance;
    
        return $this;
    }

    /**
     * Get secteur_croissance
     *
     * @return string 
     */
    public function getSecteurCroissance()
    {
        return $this->secteur_croissance;
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
     * @param \SO\StandardBundle\Entity\StdCategory $children
     * @return StdCategory
     */
    public function addChildren(\SO\StandardBundle\Entity\StdCategory $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \SO\StandardBundle\Entity\StdCategory $children
     */
    public function removeChildren(\SO\StandardBundle\Entity\StdCategory $children)
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
     * @param \SO\StandardBundle\Entity\StdCategory $parent
     * @return StdCategory
     */
    public function setParent(\SO\StandardBundle\Entity\StdCategory $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \SO\StandardBundle\Entity\StdCategory 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add CategoryProfession
     *
     * @param \SO\ProfessionBundle\Entity\Profession $categoryProfession
     * @return StdCategory
     */
    public function addCategoryProfession(\SO\ProfessionBundle\Entity\Profession $categoryProfession)
    {
        $this->CategoryProfession[] = $categoryProfession;
    
        return $this;
    }

    /**
     * Remove CategoryProfession
     *
     * @param \SO\ProfessionBundle\Entity\Profession $categoryProfession
     */
    public function removeCategoryProfession(\SO\ProfessionBundle\Entity\Profession $categoryProfession)
    {
        $this->CategoryProfession->removeElement($categoryProfession);
    }

    /**
     * Get CategoryProfession
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategoryProfession()
    {
        return $this->CategoryProfession;
    }

    /**
     * Add CategoryTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $categoryTraining
     * @return StdCategory
     */
    public function addCategoryTraining(\SO\TrainingBundle\Entity\Training $categoryTraining)
    {
        $this->CategoryTraining[] = $categoryTraining;
    
        return $this;
    }

    /**
     * Remove CategoryTraining
     *
     * @param \SO\TrainingBundle\Entity\Training $categoryTraining
     */
    public function removeCategoryTraining(\SO\TrainingBundle\Entity\Training $categoryTraining)
    {
        $this->CategoryTraining->removeElement($categoryTraining);
    }

    /**
     * Get CategoryTraining
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategoryTraining()
    {
        return $this->CategoryTraining;
    }
}