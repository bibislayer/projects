<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnaireRestriction
 */
class QuestionnaireRestriction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $date_start;

    /**
     * @var \DateTime
     */
    private $date_end;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Questionnaire
     */
    private $Questionnaire;


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
     * Set date_start
     *
     * @param \DateTime $dateStart
     * @return QuestionnaireRestriction
     */
    public function setDateStart($dateStart)
    {
        $this->date_start = $dateStart;
    
        return $this;
    }

    /**
     * Get date_start
     *
     * @return \DateTime 
     */
    public function getDateStart()
    {
        return $this->date_start;
    }

    /**
     * Set date_end
     *
     * @param \DateTime $dateEnd
     * @return QuestionnaireRestriction
     */
    public function setDateEnd($dateEnd)
    {
        $this->date_end = $dateEnd;
    
        return $this;
    }

    /**
     * Get date_end
     *
     * @return \DateTime 
     */
    public function getDateEnd()
    {
        return $this->date_end;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return QuestionnaireRestriction
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return QuestionnaireRestriction
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
}