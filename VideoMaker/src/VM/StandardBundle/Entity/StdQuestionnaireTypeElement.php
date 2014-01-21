<?php

namespace VM\StandardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StdQuestionnaireTypeElement
 */
class StdQuestionnaireTypeElement
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
     * @var \VM\QuestionnaireBundle\Entity\QuestionnaireElement
     */
    private $QuestionnaireElement;


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
     * @return StdQuestionnaireTypeElement
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
     * @return StdQuestionnaireTypeElement
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
     * Set QuestionnaireElement
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement
     * @return StdQuestionnaireTypeElement
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
     * Constructor
     */
    public function __construct()
    {
        $this->QuestionnaireElement = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add QuestionnaireElement
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement
     * @return StdQuestionnaireTypeElement
     */
    public function addQuestionnaireElement(\VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement)
    {
        $this->QuestionnaireElement[] = $questionnaireElement;
    
        return $this;
    }

    /**
     * Remove QuestionnaireElement
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement
     */
    public function removeQuestionnaireElement(\VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement)
    {
        $this->QuestionnaireElement->removeElement($questionnaireElement);
    }
}