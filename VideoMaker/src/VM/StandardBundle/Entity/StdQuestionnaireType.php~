<?php

namespace VM\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdQuestionnaireType
 */
class StdQuestionnaireType
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
     * @var \VM\QuestionnaireBundle\Entity\Questionnaire
     */
    private $Questionnaire;

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
     * @return StdQuestionnaireType
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
     * @return StdQuestionnaireType
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
     * Set Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return StdQuestionnaireType
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
     * Set Help
     *
     * @param \VM\QuestionnaireBundle\Entity\Help $help
     * @return StdQuestionnaireType
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
        $this->Questionnaire = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return StdQuestionnaireType
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
}