<?php

namespace VM\MakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdvertQuestionnaire
 */
class AdvertQuestionnaire
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \VM\MakerBundle\Entity\Advert
     */
    private $Advert;

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
     * Set Advert
     *
     * @param \VM\MakerBundle\Entity\Advert $advert
     * @return AdvertQuestionnaire
     */
    public function setAdvert(\VM\MakerBundle\Entity\Advert $advert = null)
    {
        $this->Advert = $advert;
    
        return $this;
    }

    /**
     * Get Advert
     *
     * @return \VM\MakerBundle\Entity\Advert 
     */
    public function getAdvert()
    {
        return $this->Advert;
    }

    /**
     * Set Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return AdvertQuestionnaire
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