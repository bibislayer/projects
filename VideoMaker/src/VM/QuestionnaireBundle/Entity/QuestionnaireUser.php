<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnaireUser
 */
class QuestionnaireUser
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $time_elapsed;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var string
     */
    private $comments;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Questionnaire
     */
    private $Questionnaire;

    /**
     * @var \VM\UserBundle\Entity\User
     */
    private $User;


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
     * Set time_elapsed
     *
     * @param integer $timeElapsed
     * @return QuestionnaireUser
     */
    public function setTimeElapsed($timeElapsed)
    {
        $this->time_elapsed = $timeElapsed;
    
        return $this;
    }

    /**
     * Get time_elapsed
     *
     * @return integer 
     */
    public function getTimeElapsed()
    {
        return $this->time_elapsed;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return QuestionnaireUser
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    
        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set comments
     *
     * @param string $comments
     * @return QuestionnaireUser
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    
        return $this;
    }

    /**
     * Get comments
     *
     * @return string 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return QuestionnaireUser
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return QuestionnaireUser
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
     * @return QuestionnaireUser
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
     * Set Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return QuestionnaireUser
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
     * Set User
     *
     * @param \VM\UserBundle\Entity\User $user
     * @return QuestionnaireUser
     */
    public function setUser(\VM\UserBundle\Entity\User $user = null)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get User
     *
     * @return \VM\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->User;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireQuestionResponse;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->QuestionnaireQuestionResponse = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add QuestionnaireQuestionResponse
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionResponse
     * @return QuestionnaireUser
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
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $first_name;

    /**
     * @var string
     */
    private $last_name;


    /**
     * Set email
     *
     * @param string $email
     * @return QuestionnaireUser
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     * @return QuestionnaireUser
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    
        return $this;
    }

    /**
     * Get first_name
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     * @return QuestionnaireUser
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    
        return $this;
    }

    /**
     * Get last_name
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->last_name;
    }
    /**
     * @var integer
     */
    private $current_position;


    /**
     * Set current_position
     *
     * @param integer $currentPosition
     * @return QuestionnaireUser
     */
    public function setCurrentPosition($currentPosition)
    {
        $this->current_position = $currentPosition;
    
        return $this;
    }

    /**
     * Get current_position
     *
     * @return integer 
     */
    public function getCurrentPosition()
    {
        return $this->current_position;
    }
    /**
     * @var array
     */
    private $score;


    /**
     * Set score
     *
     * @param array $score
     * @return QuestionnaireUser
     */
    public function setScore($score)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return array 
     */
    public function getScore()
    {
        return $this->score;
    }
    /**
     * @var boolean
     */
    private $as_finished;


    /**
     * Set as_finished
     *
     * @param boolean $asFinished
     * @return QuestionnaireUser
     */
    public function setAsFinished($asFinished)
    {
        $this->as_finished = $asFinished;
    
        return $this;
    }

    /**
     * Get as_finished
     *
     * @return boolean 
     */
    public function getAsFinished()
    {
        return $this->as_finished;
    }
    /**
     * @var string
     */
    private $phone_number;


    /**
     * Set phone_number
     *
     * @param string $phoneNumber
     * @return QuestionnaireUser
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phone_number = $phoneNumber;
    
        return $this;
    }

    /**
     * Get phone_number
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phone_number;
    }
}