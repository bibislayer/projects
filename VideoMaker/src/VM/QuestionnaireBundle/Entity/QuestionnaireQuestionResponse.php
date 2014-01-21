<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnaireQuestionResponse
 */
class QuestionnaireQuestionResponse
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $question_mark;

    /**
     * @var string
     */
    private $response;

    /**
     * @var string
     */
    private $enclosed_files;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \VM\QuestionnaireBundle\Entity\QuestionnaireAdministrator
     */
    private $QuestionnaireAdministrator;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Question
     */
    private $Question;

    /**
     * @var \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice
     */
    private $QuestionnaireQuestionChoice;


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
     * Set question_mark
     *
     * @param integer $questionMark
     * @return QuestionnaireQuestionResponse
     */
    public function setQuestionMark($questionMark)
    {
        $this->question_mark = $questionMark;
    
        return $this;
    }

    /**
     * Get question_mark
     *
     * @return integer 
     */
    public function getQuestionMark()
    {
        return $this->question_mark;
    }

    /**
     * Set response
     *
     * @param string $response
     * @return QuestionnaireQuestionResponse
     */
    public function setResponse($response)
    {
        $this->response = $response;
    
        return $this;
    }

    /**
     * Get response
     *
     * @return string 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set enclosed_files
     *
     * @param string $enclosedFiles
     * @return QuestionnaireQuestionResponse
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return QuestionnaireQuestionResponse
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
     * @return QuestionnaireQuestionResponse
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
     * Set QuestionnaireAdministrator
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireAdministrator $questionnaireAdministrator
     * @return QuestionnaireQuestionResponse
     */
    public function setQuestionnaireAdministrator(\VM\QuestionnaireBundle\Entity\QuestionnaireAdministrator $questionnaireAdministrator = null)
    {
        $this->QuestionnaireAdministrator = $questionnaireAdministrator;
    
        return $this;
    }

    /**
     * Get QuestionnaireAdministrator
     *
     * @return \VM\QuestionnaireBundle\Entity\QuestionnaireAdministrator 
     */
    public function getQuestionnaireAdministrator()
    {
        return $this->QuestionnaireAdministrator;
    }

    /**
     * Set Question
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $question
     * @return QuestionnaireQuestionResponse
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
     * Set QuestionnaireQuestionChoice
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $questionnaireQuestionChoice
     * @return QuestionnaireQuestionResponse
     */
    public function setQuestionnaireQuestionChoice(\VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice $questionnaireQuestionChoice = null)
    {
        $this->QuestionnaireQuestionChoice = $questionnaireQuestionChoice;
    
        return $this;
    }

    /**
     * Get QuestionnaireQuestionChoice
     *
     * @return \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionChoice 
     */
    public function getQuestionnaireQuestionChoice()
    {
        return $this->QuestionnaireQuestionChoice;
    }
    /**
     * @var \VM\QuestionnaireBundle\Entity\QuestionnaireUser
     */
    private $QuestionnaireUser;


    /**
     * Set QuestionnaireUser
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser
     * @return QuestionnaireQuestionResponse
     */
    public function setQuestionnaireUser(\VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser = null)
    {
        $this->QuestionnaireUser = $questionnaireUser;
    
        return $this;
    }

    /**
     * Get QuestionnaireUser
     *
     * @return \VM\QuestionnaireBundle\Entity\QuestionnaireUser 
     */
    public function getQuestionnaireUser()
    {
        return $this->QuestionnaireUser;
    }
    /**
     * @var \DateTime
     */
    private $start_date;

    /**
     * @var \DateTime
     */
    private $end_date;


    /**
     * Set start_date
     *
     * @param \DateTime $startDate
     * @return QuestionnaireQuestionResponse
     */
    public function setStartDate($startDate)
    {
        $this->start_date = $startDate;
    
        return $this;
    }

    /**
     * Get start_date
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * Set end_date
     *
     * @param \DateTime $endDate
     * @return QuestionnaireQuestionResponse
     */
    public function setEndDate($endDate)
    {
        $this->end_date = $endDate;
    
        return $this;
    }

    /**
     * Get end_date
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->end_date;
    }
}