<?php

namespace VM\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdQuestionType
 */
class StdQuestionType
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
     * @var \VM\QuestionnaireBundle\Entity\Question
     */
    private $Question;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Help
     */
    private $Help;


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
     * @return StdQuestionType
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
     * @return StdQuestionType
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
     * Set Question
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $question
     * @return StdQuestionType
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
     * Set Help
     *
     * @param \VM\QuestionnaireBundle\Entity\Help $help
     * @return StdQuestionType
     */
    public function setHelp(\VM\QuestionnaireBundle\Entity\Help $help = null)
    {
        $this->Help = $help;
    
        return $this;
    }

    /**
     * Get Help
     *
     * @return \VM\QuestionnaireBundle\Entity\Help 
     */
    public function getHelp()
    {
        return $this->Help;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Question = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add Question
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $question
     * @return StdQuestionType
     */
    public function addQuestion(\VM\QuestionnaireBundle\Entity\Question $question)
    {
        $this->Question[] = $question;
    
        return $this;
    }

    /**
     * Remove Question
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $question
     */
    public function removeQuestion(\VM\QuestionnaireBundle\Entity\Question $question)
    {
        $this->Question->removeElement($question);
    }
    /**
     * @var string
     */
    private $template;


    /**
     * Set template
     *
     * @param string $template
     * @return StdQuestionType
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    
        return $this;
    }

    /**
     * Get template
     *
     * @return string 
     */
    public function getTemplate()
    {
        return $this->template;
    }
    /**
     * @var integer
     */
    private $is_active;


    /**
     * Set is_active
     *
     * @param integer $isActive
     * @return StdQuestionType
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
    
        return $this;
    }

    /**
     * Get is_active
     *
     * @return integer 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }
}