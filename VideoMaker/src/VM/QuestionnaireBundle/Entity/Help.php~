<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Help
 */
class Help
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Questionnaire;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Question;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $StdQuestionType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $StdQuestionnaireType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Questionnaire = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Question = new \Doctrine\Common\Collections\ArrayCollection();
        $this->StdQuestionType = new \Doctrine\Common\Collections\ArrayCollection();
        $this->StdQuestionnaireType = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set text
     *
     * @param string $text
     * @return Help
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
     * Add Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return Help
     */
    public function addQuestionnaire(\VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire)
    {
        $this->Questionnaire[] = $questionnaire;
    
        return $this;
    }

    /**
     * Remove Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     */
    public function removeQuestionnaire(\VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire)
    {
        $this->Questionnaire->removeElement($questionnaire);
    }

    /**
     * Get Questionnaire
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaire()
    {
        return $this->Questionnaire;
    }

    /**
     * Add Question
     *
     * @param \VM\QuestionnaireBundle\Entity\Question $question
     * @return Help
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
     * Get Question
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestion()
    {
        return $this->Question;
    }

    /**
     * Add StdQuestionType
     *
     * @param \VM\StandardBundle\Entity\StdQuestionType $stdQuestionType
     * @return Help
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
     * Add StdQuestionnaireType
     *
     * @param \VM\StandardBundle\Entity\StdQuestionnaireType $stdQuestionnaireType
     * @return Help
     */
    public function addStdQuestionnaireType(\VM\StandardBundle\Entity\StdQuestionnaireType $stdQuestionnaireType)
    {
        $this->StdQuestionnaireType[] = $stdQuestionnaireType;
    
        return $this;
    }

    /**
     * Remove StdQuestionnaireType
     *
     * @param \VM\StandardBundle\Entity\StdQuestionnaireType $stdQuestionnaireType
     */
    public function removeStdQuestionnaireType(\VM\StandardBundle\Entity\StdQuestionnaireType $stdQuestionnaireType)
    {
        $this->StdQuestionnaireType->removeElement($stdQuestionnaireType);
    }

    /**
     * Get StdQuestionnaireType
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStdQuestionnaireType()
    {
        return $this->StdQuestionnaireType;
    }
}