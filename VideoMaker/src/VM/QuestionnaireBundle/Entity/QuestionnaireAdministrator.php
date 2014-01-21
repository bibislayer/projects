<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnaireAdministrator
 */
class QuestionnaireAdministrator
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireQuestionResponse;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Questionnaire
     */
    private $Questionnaire;

    /**
     * @var \VM\UserBundle\Entity\User
     */
    private $User;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->QuestionnaireQuestionResponse = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set roles
     *
     * @param array $roles
     * @return QuestionnaireAdministrator
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    
        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add QuestionnaireQuestionResponse
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireQuestionResponse $questionnaireQuestionResponse
     * @return QuestionnaireAdministrator
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
     * Set Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return QuestionnaireAdministrator
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
     * @return QuestionnaireAdministrator
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
}