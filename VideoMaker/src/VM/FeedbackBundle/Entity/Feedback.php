<?php

namespace VM\FeedbackBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Feedback
 */
class Feedback
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $text;

    /**
     * @var boolean
     */
    private $is_read;

    /**
     * @var boolean
     */
    private $responsed;

    /**
     * @var boolean
     */
    private $resolved;

    /**
     * @var \VM\UserBundle\Entity\User
     */
    private $User;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Question
     */
    private $Question;


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
     * Set email
     *
     * @param string $email
     * @return Feedback
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
     * Set text
     *
     * @param string $text
     * @return Feedback
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set is_read
     *
     * @param boolean $isRead
     * @return Feedback
     */
    public function setIsRead($isRead)
    {
        $this->is_read = $isRead;
    
        return $this;
    }

    /**
     * Get is_read
     *
     * @return boolean 
     */
    public function getIsRead()
    {
        return $this->is_read;
    }

    /**
     * Set responsed
     *
     * @param boolean $responsed
     * @return Feedback
     */
    public function setResponsed($responsed)
    {
        $this->responsed = $responsed;
    
        return $this;
    }

    /**
     * Get responsed
     *
     * @return boolean 
     */
    public function getResponsed()
    {
        return $this->responsed;
    }

    /**
     * Set resolved
     *
     * @param boolean $resolved
     * @return Feedback
     */
    public function setResolved($resolved)
    {
        $this->resolved = $resolved;
    
        return $this;
    }

    /**
     * Get resolved
     *
     * @return boolean 
     */
    public function getResolved()
    {
        return $this->resolved;
    }

    /**
     * Set User
     *
     * @param \VM\UserBundle\Entity\User $user
     * @return Feedback
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
     * Set Question
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $question
     * @return Feedback
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
     * @var string
     */
    private $text_comment;


    /**
     * Set text_comment
     *
     * @param string $textComment
     * @return Feedback
     */
    public function setTextComment($textComment)
    {
        $this->text_comment = $textComment;
    
        return $this;
    }

    /**
     * Get text_comment
     *
     * @return string 
     */
    public function getTextComment()
    {
        return $this->text_comment;
    }
}