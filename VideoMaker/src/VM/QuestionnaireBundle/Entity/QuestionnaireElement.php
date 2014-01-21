<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnaireElement
 */
class QuestionnaireElement
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
     * @var array
     */
    private $tag;

    /**
     * @var boolean
     */
    private $display_page;

    /**
     * @var boolean
     */
    private $display_next;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireElementPayment;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Questionnaire
     */
    private $Questionnaire;

    /**
     * @var \VM\QuestionnaireBundle\Entity\QuestionnaireElement
     */
    private $parent;

    /**
     * @var \VM\StandardBundle\Entity\StdQuestionnaireTypeElement
     */
    private $StdQuestionnaireTypeElement;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->QuestionnaireElementPayment = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * Set tag
     *
     * @param array $tag
     * @return QuestionnaireElement
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
     * Set display_page
     *
     * @param boolean $displayPage
     * @return QuestionnaireElement
     */
    public function setDisplayPage($displayPage)
    {
        $this->display_page = $displayPage;
    
        return $this;
    }

    /**
     * Get display_page
     *
     * @return boolean 
     */
    public function getDisplayPage()
    {
        return $this->display_page;
    }

    /**
     * Set display_next
     *
     * @param boolean $displayNext
     * @return QuestionnaireElement
     */
    public function setDisplayNext($displayNext)
    {
        $this->display_next = $displayNext;
    
        return $this;
    }

    /**
     * Get display_next
     *
     * @return boolean 
     */
    public function getDisplayNext()
    {
        return $this->display_next;
    }

    /**
     * Set enclosed_files
     *
     * @param string $enclosedFiles
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * @return QuestionnaireElement
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
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $children
     * @return QuestionnaireElement
     */
    public function addChildren(\VM\QuestionnaireBundle\Entity\QuestionnaireElement $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $children
     */
    public function removeChildren(\VM\QuestionnaireBundle\Entity\QuestionnaireElement $children)
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
     * Add QuestionnaireElementPayment
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElementPayment $questionnaireElementPayment
     * @return QuestionnaireElement
     */
    public function addQuestionnaireElementPayment(\VM\QuestionnaireBundle\Entity\QuestionnaireElementPayment $questionnaireElementPayment)
    {
        $this->QuestionnaireElementPayment[] = $questionnaireElementPayment;
    
        return $this;
    }

    /**
     * Remove QuestionnaireElementPayment
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElementPayment $questionnaireElementPayment
     */
    public function removeQuestionnaireElementPayment(\VM\QuestionnaireBundle\Entity\QuestionnaireElementPayment $questionnaireElementPayment)
    {
        $this->QuestionnaireElementPayment->removeElement($questionnaireElementPayment);
    }

    /**
     * Get QuestionnaireElementPayment
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireElementPayment()
    {
        return $this->QuestionnaireElementPayment;
    }

    /**
     * Set Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return QuestionnaireElement
     */
    public function setQuestionnaire(\VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire = null)
    {
        $this->Questionnaire = $questionnaire;
    
        return $this;
    }

    /**
     * Get Questionnaire
     *
     * @return \VM\QuestionnaireBundle\Entity\Questionnaire 
     */
    public function getQuestionnaire()
    {
        return $this->Questionnaire;
    }

    /**
     * Set parent
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $parent
     * @return QuestionnaireElement
     */
    public function setParent(\VM\QuestionnaireBundle\Entity\QuestionnaireElement $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \VM\QuestionnaireBundle\Entity\QuestionnaireElement 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set StdQuestionnaireTypeElement
     *
     * @param \VM\StandardBundle\Entity\StdQuestionnaireTypeElement $stdQuestionnaireTypeElement
     * @return QuestionnaireElement
     */
    public function setStdQuestionnaireTypeElement(\VM\StandardBundle\Entity\StdQuestionnaireTypeElement $stdQuestionnaireTypeElement = null)
    {
        $this->StdQuestionnaireTypeElement = $stdQuestionnaireTypeElement;
    
        return $this;
    }

    /**
     * Get StdQuestionnaireTypeElement
     *
     * @return \VM\StandardBundle\Entity\StdQuestionnaireTypeElement 
     */
    public function getStdQuestionnaireTypeElement()
    {
        return $this->StdQuestionnaireTypeElement;
    }
    /**
     * @var \VM\QuestionnaireBundle\Entity\Question
     */
    private $Question;


    /**
     * Set Question
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $question
     * @return QuestionnaireElement
     */
    public function setQuestion(\VM\QuestionnaireBundle\Entity\Question $question = null)
    {
        $this->Question = $question;
    
        return $this;
    }

    /**
     * Get Question
     *
     * @return \VM\QuestionnaireBundle\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->Question;
    }
    /**
     * @var integer
     */
    private $time_limit;


    /**
     * Set time_limit
     *
     * @param integer $timeLimit
     * @return QuestionnaireElement
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
     * @var string
     */
    private $media_type;


    /**
     * Set media_type
     *
     * @param string $mediaType
     * @return QuestionnaireElement
     */
    public function setMediaType($mediaType)
    {
        $this->media_type = $mediaType;
    
        return $this;
    }

    /**
     * Get media_type
     *
     * @return string 
     */
    public function getMediaType()
    {
        return $this->media_type;
    }
    /**
     * @var boolean
     */
    private $media_allow;


    /**
     * Set media_allow
     *
     * @param boolean $mediaAllow
     * @return QuestionnaireElement
     */
    public function setMediaAllow($mediaAllow)
    {
        $this->media_allow = $mediaAllow;
    
        return $this;
    }

    /**
     * Get media_allow
     *
     * @return boolean 
     */
    public function getMediaAllow()
    {
        return $this->media_allow;
    }
}