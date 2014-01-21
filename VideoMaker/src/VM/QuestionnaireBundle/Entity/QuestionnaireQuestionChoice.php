<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnaireQuestionChoice
 */
class QuestionnaireQuestionChoice
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
    private $position;

    /**
     * @var integer
     */
    private $ranking;

    /**
     * @var boolean
     */
    private $good_response;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireQuestionResponse;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice
     */
    private $NextQuestion;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Question
     */
    private $Question;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->QuestionnaireQuestionResponse = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return QuestionnaireQuestionChoice
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
     * Set position
     *
     * @param integer $position
     * @return QuestionnaireQuestionChoice
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
     * Set ranking
     *
     * @param integer $ranking
     * @return QuestionnaireQuestionChoice
     */
    public function setRanking($ranking)
    {
        $this->ranking = $ranking;
    
        return $this;
    }

    /**
     * Get ranking
     *
     * @return integer 
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * Set good_response
     *
     * @param boolean $goodResponse
     * @return QuestionnaireQuestionChoice
     */
    public function setGoodResponse($goodResponse)
    {
        $this->good_response = $goodResponse;
    
        return $this;
    }

    /**
     * Get good_response
     *
     * @return boolean 
     */
    public function getGoodResponse()
    {
        return $this->good_response;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return QuestionnaireQuestionChoice
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
     * @return QuestionnaireQuestionChoice
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
     * Add QuestionnaireQuestionResponse
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionResponse
     * @return QuestionnaireQuestionChoice
     */
    public function addQuestionnaireQuestionResponse(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionResponse)
    {
        $this->QuestionnaireQuestionResponse[] = $questionnaireQuestionResponse;
    
        return $this;
    }

    /**
     * Remove QuestionnaireQuestionResponse
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionResponse
     */
    public function removeQuestionnaireQuestionResponse(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionResponse)
    {
        $this->QuestionnaireQuestionResponse->removeElement($questionnaireQuestionResponse);
    }

    /**
     * Get QuestionnaireQuestionResponse
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireQuestionResponse()
    {
        return $this->QuestionnaireQuestionResponse;
    }

    /**
     * Add children
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $children
     * @return QuestionnaireQuestionChoice
     */
    public function addChildren(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $children
     */
    public function removeChildren(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $children)
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
     * Set NextQuestion
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $nextQuestion
     * @return QuestionnaireQuestionChoice
     */
    public function setNextQuestion(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $nextQuestion = null)
    {
        $this->NextQuestion = $nextQuestion;
    
        return $this;
    }

    /**
     * Get NextQuestion
     *
     * @return \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice 
     */
    public function getNextQuestion()
    {
        return $this->NextQuestion;
    }

    /**
     * Set Question
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $question
     * @return QuestionnaireQuestionChoice
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireQuestionChoice;


    /**
     * Add QuestionnaireQuestionChoice
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionChoice
     * @return QuestionnaireQuestionChoice
     */
    public function addQuestionnaireQuestionChoice(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionChoice)
    {
        $this->QuestionnaireQuestionChoice[] = $questionnaireQuestionChoice;
    
        return $this;
    }

    /**
     * Remove QuestionnaireQuestionChoice
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionChoice
     */
    public function removeQuestionnaireQuestionChoice(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionChoice)
    {
        $this->QuestionnaireQuestionChoice->removeElement($questionnaireQuestionChoice);
    }

    /**
     * Get QuestionnaireQuestionChoice
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireQuestionChoice()
    {
        return $this->QuestionnaireQuestionChoice;
    }

    /**
     * Set QuestionnaireQuestionResponse
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $questionnaireQuestionResponse
     * @return QuestionnaireQuestionChoice
     */
    public function setQuestionnaireQuestionResponse(\VM\QuestionnaireBundle\Entity\Question $questionnaireQuestionResponse = null)
    {
        $this->QuestionnaireQuestionResponse = $questionnaireQuestionResponse;
    
        return $this;
    }
    /**
     * @var integer
     */
    private $type;


    /**
     * Set type
     *
     * @param integer $type
     * @return QuestionnaireQuestionChoice
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}