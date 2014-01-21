<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 */
class Question
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $rankin;

    /**
     * @var integer
     */
    private $question_time;

    /**
     * @var integer
     */
    private $response_time;

    /**
     * @var string
     */
    private $is_conditional;

    /**
     * @var integer
     */
    private $time_limit;

    /**
     * @var boolean
     */
    private $eliminate_question;

    /**
     * @var boolean
     */
    private $anti_plagiat;

    /**
     * @var boolean
     */
    private $needed;

    /**
     * @var integer
     */
    private $char_limit;

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
    private $QuestionnaireQuestionChoice;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Help;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Feedback;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $StdQuestionType;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Questionnaire
     */
    private $Questionnaire;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->QuestionnaireQuestionResponse = new \Doctrine\Common\Collections\ArrayCollection();
        $this->QuestionnaireQuestionChoice = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Help = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Feedback = new \Doctrine\Common\Collections\ArrayCollection();
        $this->StdQuestionType = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set rankin
     *
     * @param integer $rankin
     * @return Question
     */
    public function setRankin($rankin)
    {
        $this->rankin = $rankin;
    
        return $this;
    }

    /**
     * Get rankin
     *
     * @return integer 
     */
    public function getRankin()
    {
        return $this->rankin;
    }

    /**
     * Set question_time
     *
     * @param integer $questionTime
     * @return Question
     */
    public function setQuestionTime($questionTime)
    {
        $this->question_time = $questionTime;
    
        return $this;
    }

    /**
     * Get question_time
     *
     * @return integer 
     */
    public function getQuestionTime()
    {
        return $this->question_time;
    }

    /**
     * Set response_time
     *
     * @param integer $responseTime
     * @return Question
     */
    public function setResponseTime($responseTime)
    {
        $this->response_time = $responseTime;
    
        return $this;
    }

    /**
     * Get response_time
     *
     * @return integer 
     */
    public function getResponseTime()
    {
        return $this->response_time;
    }

    /**
     * Set is_conditional
     *
     * @param string $isConditional
     * @return Question
     */
    public function setIsConditional($isConditional)
    {
        $this->is_conditional = $isConditional;
    
        return $this;
    }

    /**
     * Get is_conditional
     *
     * @return string 
     */
    public function getIsConditional()
    {
        return $this->is_conditional;
    }

    /**
     * Set time_limit
     *
     * @param integer $timeLimit
     * @return Question
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
     * Set eliminate_question
     *
     * @param boolean $eliminateQuestion
     * @return Question
     */
    public function setEliminateQuestion($eliminateQuestion)
    {
        $this->eliminate_question = $eliminateQuestion;
    
        return $this;
    }

    /**
     * Get eliminate_question
     *
     * @return boolean 
     */
    public function getEliminateQuestion()
    {
        return $this->eliminate_question;
    }

    /**
     * Set anti_plagiat
     *
     * @param boolean $antiPlagiat
     * @return Question
     */
    public function setAntiPlagiat($antiPlagiat)
    {
        $this->anti_plagiat = $antiPlagiat;
    
        return $this;
    }

    /**
     * Get anti_plagiat
     *
     * @return boolean 
     */
    public function getAntiPlagiat()
    {
        return $this->anti_plagiat;
    }

    /**
     * Set needed
     *
     * @param boolean $needed
     * @return Question
     */
    public function setNeeded($needed)
    {
        $this->needed = $needed;
    
        return $this;
    }

    /**
     * Get needed
     *
     * @return boolean 
     */
    public function getNeeded()
    {
        return $this->needed;
    }

    /**
     * Set char_limit
     *
     * @param integer $charLimit
     * @return Question
     */
    public function setCharLimit($charLimit)
    {
        $this->char_limit = $charLimit;
    
        return $this;
    }

    /**
     * Get char_limit
     *
     * @return integer 
     */
    public function getCharLimit()
    {
        return $this->char_limit;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Question
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
     * @return Question
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
     * @return Question
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
     * Add QuestionnaireQuestionChoice
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $questionnaireQuestionChoice
     * @return Question
     */
    public function addQuestionnaireQuestionChoice(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $questionnaireQuestionChoice)
    {
        $this->QuestionnaireQuestionChoice[] = $questionnaireQuestionChoice;
    
        return $this;
    }

    /**
     * Remove QuestionnaireQuestionChoice
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $questionnaireQuestionChoice
     */
    public function removeQuestionnaireQuestionChoice(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $questionnaireQuestionChoice)
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
     * Add Help
     *
     * @param \VM\QuestionnaireBundle\Entity\Help $help
     * @return Question
     */
    public function addHelp(\VM\QuestionnaireBundle\Entity\Help $help)
    {
        $this->Help[] = $help;
    
        return $this;
    }

    /**
     * Remove Help
     *
     * @param \VM\QuestionnaireBundle\Entity\Help $help
     */
    public function removeHelp(\VM\QuestionnaireBundle\Entity\Help $help)
    {
        $this->Help->removeElement($help);
    }

    /**
     * Get Help
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHelp()
    {
        return $this->Help;
    }

    /**
     * Add Feedback
     *
     * @param \VM\FeedbackBundle\Entity\Feedback $feedback
     * @return Question
     */
    public function addFeedback(\VM\FeedbackBundle\Entity\Feedback $feedback)
    {
        $this->Feedback[] = $feedback;
    
        return $this;
    }

    /**
     * Remove Feedback
     *
     * @param \VM\FeedbackBundle\Entity\Feedback $feedback
     */
    public function removeFeedback(\VM\FeedbackBundle\Entity\Feedback $feedback)
    {
        $this->Feedback->removeElement($feedback);
    }

    /**
     * Get Feedback
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeedback()
    {
        return $this->Feedback;
    }

    /**
     * Add StdQuestionType
     *
     * @param \VM\StandardBundle\Entity\StdQuestionType $stdQuestionType
     * @return Question
     */
    public function addStdQuestionType(\VM\StandardBundle\Entity\StdQuestionType $stdQuestionType)
    {
        $this->StdQuestionType[] = $stdQuestionType;
    
        return $this;
    }

    /**
     * Remove StdQuestionType
     *
     * @param \VM\StandardBundle\Entity\StdQuestionType $stdQuestionType
     */
    public function removeStdQuestionType(\VM\StandardBundle\Entity\StdQuestionType $stdQuestionType)
    {
        $this->StdQuestionType->removeElement($stdQuestionType);
    }

    /**
     * Get StdQuestionType
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStdQuestionType()
    {
        return $this->StdQuestionType;
    }

    /**
     * Set Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return Question
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
     * Set StdQuestionType
     *
     * @param \VM\StandardBundle\Entity\StdQuestionType $stdQuestionType
     * @return Question
     */
    public function setStdQuestionType(\VM\StandardBundle\Entity\StdQuestionType $stdQuestionType = null)
    {
        $this->StdQuestionType = $stdQuestionType;
    
        return $this;
    }
    /**
     * @var string
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     * @return Question
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
     * @var \VM\QuestionnaireBundle\Entity\QuestionnaireElement
     */
    private $QuestionnaireElement;


    /**
     * Set QuestionnaireElement
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement
     * @return Question
     */
    public function setQuestionnaireElement(\VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement = null)
    {
        $this->QuestionnaireElement = $questionnaireElement;
    
        return $this;
    }

    /**
     * Get QuestionnaireElement
     *
     * @return \VM\QuestionnaireBundle\Entity\QuestionnaireElement 
     */
    public function getQuestionnaireElement()
    {
        return $this->QuestionnaireElement;
    }
   

    /**
     * Set Help
     *
     * @param \VM\QuestionnaireBundle\Entity\Help $help
     * @return Question
     */
    public function setHelp(\VM\QuestionnaireBundle\Entity\Help $help = null)
    {
        $this->Help = $help;
    
        return $this;
    }


    /**
     * @var array
     */
    private $options;


    /**
     * Set options
     *
     * @param array $options
     * @return Question
     */
    public function setOptions($options)
    {
        $this->options = $options;
    
        return $this;
    }

    /**
     * Get options
     *
     * @return array 
     */
    public function getOptions()
    {
        return $this->options;
    }
}