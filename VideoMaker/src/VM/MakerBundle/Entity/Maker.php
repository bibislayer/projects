<?php

namespace VM\MakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Maker
 */
class Maker
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
     * @var array
     */
    private $status;

    /**
     * @var integer
     */
    private $credit;

    /**
     * @var integer
     */
    private $approbation;

    /**
     * @var integer
     */
    private $published;

    /**
     * @var \DateTime
     */
    private $published_at;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MakerAdministrator;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Advert;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MakerInvitationAdministrator;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MakerNote;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $CreditHistory;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Calendar;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Questionnaire;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->MakerAdministrator = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Advert = new \Doctrine\Common\Collections\ArrayCollection();
        $this->MakerInvitationAdministrator = new \Doctrine\Common\Collections\ArrayCollection();
        $this->MakerNote = new \Doctrine\Common\Collections\ArrayCollection();
        $this->CreditHistory = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Calendar = new \Doctrine\Common\Collections\ArrayCollection();
        $this->Questionnaire = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Maker
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
     * Set status
     *
     * @param array $status
     * @return Maker
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return array 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set credit
     *
     * @param integer $credit
     * @return Maker
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
    
        return $this;
    }

    /**
     * Get credit
     *
     * @return integer 
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set approbation
     *
     * @param integer $approbation
     * @return Maker
     */
    public function setApprobation($approbation)
    {
        $this->approbation = $approbation;
    
        return $this;
    }

    /**
     * Get approbation
     *
     * @return integer 
     */
    public function getApprobation()
    {
        return $this->approbation;
    }

    /**
     * Set published
     *
     * @param integer $published
     * @return Maker
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return integer 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set published_at
     *
     * @param \DateTime $publishedAt
     * @return Maker
     */
    public function setPublishedAt($publishedAt)
    {
        $this->published_at = $publishedAt;
    
        return $this;
    }

    /**
     * Get published_at
     *
     * @return \DateTime 
     */
    public function getPublishedAt()
    {
        return $this->published_at;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Maker
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
     * @return Maker
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
     * Set slug
     *
     * @param string $slug
     * @return Maker
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
     * Add MakerAdministrator
     *
     * @param \VM\MakerBundle\Entity\MakerAdministrator $makerAdministrator
     * @return Maker
     */
    public function addMakerAdministrator(\VM\MakerBundle\Entity\MakerAdministrator $makerAdministrator)
    {
        $this->MakerAdministrator[] = $makerAdministrator;
    
        return $this;
    }

    /**
     * Remove MakerAdministrator
     *
     * @param \VM\MakerBundle\Entity\MakerAdministrator $makerAdministrator
     */
    public function removeMakerAdministrator(\VM\MakerBundle\Entity\MakerAdministrator $makerAdministrator)
    {
        $this->MakerAdministrator->removeElement($makerAdministrator);
    }

    /**
     * Get MakerAdministrator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMakerAdministrator()
    {
        return $this->MakerAdministrator;
    }

    /**
     * Add Advert
     *
     * @param \VM\MakerBundle\Entity\Advert $advert
     * @return Maker
     */
    public function addAdvert(\VM\MakerBundle\Entity\Advert $advert)
    {
        $this->Advert[] = $advert;
    
        return $this;
    }

    /**
     * Remove Advert
     *
     * @param \VM\MakerBundle\Entity\Advert $advert
     */
    public function removeAdvert(\VM\MakerBundle\Entity\Advert $advert)
    {
        $this->Advert->removeElement($advert);
    }

    /**
     * Get Advert
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdvert()
    {
        return $this->Advert;
    }

    /**
     * Add MakerInvitationAdministrator
     *
     * @param \VM\MakerBundle\Entity\MakerInvitationAdministrator $makerInvitationAdministrator
     * @return Maker
     */
    public function addMakerInvitationAdministrator(\VM\MakerBundle\Entity\MakerInvitationAdministrator $makerInvitationAdministrator)
    {
        $this->MakerInvitationAdministrator[] = $makerInvitationAdministrator;
    
        return $this;
    }

    /**
     * Remove MakerInvitationAdministrator
     *
     * @param \VM\MakerBundle\Entity\MakerInvitationAdministrator $makerInvitationAdministrator
     */
    public function removeMakerInvitationAdministrator(\VM\MakerBundle\Entity\MakerInvitationAdministrator $makerInvitationAdministrator)
    {
        $this->MakerInvitationAdministrator->removeElement($makerInvitationAdministrator);
    }

    /**
     * Get MakerInvitationAdministrator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMakerInvitationAdministrator()
    {
        return $this->MakerInvitationAdministrator;
    }

    /**
     * Add MakerNote
     *
     * @param \VM\MakerBundle\Entity\MakerNote $makerNote
     * @return Maker
     */
    public function addMakerNote(\VM\MakerBundle\Entity\MakerNote $makerNote)
    {
        $this->MakerNote[] = $makerNote;
    
        return $this;
    }

    /**
     * Remove MakerNote
     *
     * @param \VM\MakerBundle\Entity\MakerNote $makerNote
     */
    public function removeMakerNote(\VM\MakerBundle\Entity\MakerNote $makerNote)
    {
        $this->MakerNote->removeElement($makerNote);
    }

    /**
     * Get MakerNote
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMakerNote()
    {
        return $this->MakerNote;
    }

    /**
     * Add CreditHistory
     *
     * @param \VM\CustomerBundle\Entity\CreditHistory $creditHistory
     * @return Maker
     */
    public function addCreditHistory(\VM\CustomerBundle\Entity\CreditHistory $creditHistory)
    {
        $this->CreditHistory[] = $creditHistory;
    
        return $this;
    }

    /**
     * Remove CreditHistory
     *
     * @param \VM\CustomerBundle\Entity\CreditHistory $creditHistory
     */
    public function removeCreditHistory(\VM\CustomerBundle\Entity\CreditHistory $creditHistory)
    {
        $this->CreditHistory->removeElement($creditHistory);
    }

    /**
     * Get CreditHistory
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCreditHistory()
    {
        return $this->CreditHistory;
    }

    /**
     * Add Calendar
     *
     * @param \VM\CalendarBundle\Entity\Calendar $calendar
     * @return Maker
     */
    public function addCalendar(\VM\CalendarBundle\Entity\Calendar $calendar)
    {
        $this->Calendar[] = $calendar;
    
        return $this;
    }

    /**
     * Remove Calendar
     *
     * @param \VM\CalendarBundle\Entity\Calendar $calendar
     */
    public function removeCalendar(\VM\CalendarBundle\Entity\Calendar $calendar)
    {
        $this->Calendar->removeElement($calendar);
    }

    /**
     * Get Calendar
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCalendar()
    {
        return $this->Calendar;
    }

    /**
     * Add Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return Maker
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
}