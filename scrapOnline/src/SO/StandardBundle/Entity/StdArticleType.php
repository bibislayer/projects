<?php

namespace SO\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdArticleType
 */
class StdArticleType
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
    private $Article;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Article = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return StdArticleType
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
     * @return StdArticleType
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
     * Add Article
     *
     * @param \SO\ArticleBundle\Entity\Article $article
     * @return StdArticleType
     */
    public function addArticle(\SO\ArticleBundle\Entity\Article $article)
    {
        $this->Article[] = $article;
    
        return $this;
    }

    /**
     * Remove Article
     *
     * @param \SO\ArticleBundle\Entity\Article $article
     */
    public function removeArticle(\SO\ArticleBundle\Entity\Article $article)
    {
        $this->Article->removeElement($article);
    }

    /**
     * Get Article
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getArticle()
    {
        return $this->Article;
    }
}