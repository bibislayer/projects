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
     * @var string
     */
    private $company_name;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var string
     */
    private $code_naf;

    /**
     * @var string
     */
    private $code_siret;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var string
     */
    private $url_site;

    /**
     * @var string
     */
    private $text_introduction;

    /**
     * @var string
     */
    private $text_presentation;

    /**
     * @var integer
     */
    private $year_creation;

    /**
     * @var integer
     */
    private $std_special_status_id;

    /**
     * @var integer
     */
    private $approbation;

    /**
     * @var integer
     */
    private $school_published;

    /**
     * @var string
     */
    private $status_commercial;

    /**
     * @var integer
     */
    private $published;

    /**
     * @var \DateTime
     */
    private $published_at;

    /**
     * @var boolean
     */
    private $training_establishment;

    /**
     * @var boolean
     */
    private $human_ressources;

    /**
     * @var boolean
     */
    private $credit_organization;

    /**
     * @var boolean
     */
    private $events_organizer;

    /**
     * @var boolean
     */
    private $entrance_exams_organizer;

    /**
     * @var boolean
     */
    private $recruitment_agency;

    /**
     * @var boolean
     */
    private $has_association;

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
     * Constructor
     */
    public function __construct()
    {
        $this->MakerAdministrator = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set company_name
     *
     * @param string $companyName
     * @return Maker
     */
    public function setCompanyName($companyName)
    {
        $this->company_name = $companyName;
    
        return $this;
    }

    /**
     * Get company_name
     *
     * @return string 
     */
    public function getCompanyName()
    {
        return $this->company_name;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Maker
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    
        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set code_naf
     *
     * @param string $codeNaf
     * @return Maker
     */
    public function setCodeNaf($codeNaf)
    {
        $this->code_naf = $codeNaf;
    
        return $this;
    }

    /**
     * Get code_naf
     *
     * @return string 
     */
    public function getCodeNaf()
    {
        return $this->code_naf;
    }

    /**
     * Set code_siret
     *
     * @param string $codeSiret
     * @return Maker
     */
    public function setCodeSiret($codeSiret)
    {
        $this->code_siret = $codeSiret;
    
        return $this;
    }

    /**
     * Get code_siret
     *
     * @return string 
     */
    public function getCodeSiret()
    {
        return $this->code_siret;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Maker
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return Maker
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    
        return $this;
    }

    /**
     * Get fax
     *
     * @return string 
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set url_site
     *
     * @param string $urlSite
     * @return Maker
     */
    public function setUrlSite($urlSite)
    {
        $this->url_site = $urlSite;
    
        return $this;
    }

    /**
     * Get url_site
     *
     * @return string 
     */
    public function getUrlSite()
    {
        return $this->url_site;
    }

    /**
     * Set text_introduction
     *
     * @param string $textIntroduction
     * @return Maker
     */
    public function setTextIntroduction($textIntroduction)
    {
        $this->text_introduction = $textIntroduction;
    
        return $this;
    }

    /**
     * Get text_introduction
     *
     * @return string 
     */
    public function getTextIntroduction()
    {
        return $this->text_introduction;
    }

    /**
     * Set text_presentation
     *
     * @param string $textPresentation
     * @return Maker
     */
    public function setTextPresentation($textPresentation)
    {
        $this->text_presentation = $textPresentation;
    
        return $this;
    }

    /**
     * Get text_presentation
     *
     * @return string 
     */
    public function getTextPresentation()
    {
        return $this->text_presentation;
    }

    /**
     * Set year_creation
     *
     * @param integer $yearCreation
     * @return Maker
     */
    public function setYearCreation($yearCreation)
    {
        $this->year_creation = $yearCreation;
    
        return $this;
    }

    /**
     * Get year_creation
     *
     * @return integer 
     */
    public function getYearCreation()
    {
        return $this->year_creation;
    }

    /**
     * Set std_special_status_id
     *
     * @param integer $stdSpecialStatusId
     * @return Maker
     */
    public function setStdSpecialStatusId($stdSpecialStatusId)
    {
        $this->std_special_status_id = $stdSpecialStatusId;
    
        return $this;
    }

    /**
     * Get std_special_status_id
     *
     * @return integer 
     */
    public function getStdSpecialStatusId()
    {
        return $this->std_special_status_id;
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
     * Set school_published
     *
     * @param integer $schoolPublished
     * @return Maker
     */
    public function setSchoolPublished($schoolPublished)
    {
        $this->school_published = $schoolPublished;
    
        return $this;
    }

    /**
     * Get school_published
     *
     * @return integer 
     */
    public function getSchoolPublished()
    {
        return $this->school_published;
    }

    /**
     * Set status_commercial
     *
     * @param string $statusCommercial
     * @return Maker
     */
    public function setStatusCommercial($statusCommercial)
    {
        $this->status_commercial = $statusCommercial;
    
        return $this;
    }

    /**
     * Get status_commercial
     *
     * @return string 
     */
    public function getStatusCommercial()
    {
        return $this->status_commercial;
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
     * Set training_establishment
     *
     * @param boolean $trainingEstablishment
     * @return Maker
     */
    public function setTrainingEstablishment($trainingEstablishment)
    {
        $this->training_establishment = $trainingEstablishment;
    
        return $this;
    }

    /**
     * Get training_establishment
     *
     * @return boolean 
     */
    public function getTrainingEstablishment()
    {
        return $this->training_establishment;
    }

    /**
     * Set human_ressources
     *
     * @param boolean $humanRessources
     * @return Maker
     */
    public function setHumanRessources($humanRessources)
    {
        $this->human_ressources = $humanRessources;
    
        return $this;
    }

    /**
     * Get human_ressources
     *
     * @return boolean 
     */
    public function getHumanRessources()
    {
        return $this->human_ressources;
    }

    /**
     * Set credit_organization
     *
     * @param boolean $creditOrganization
     * @return Maker
     */
    public function setCreditOrganization($creditOrganization)
    {
        $this->credit_organization = $creditOrganization;
    
        return $this;
    }

    /**
     * Get credit_organization
     *
     * @return boolean 
     */
    public function getCreditOrganization()
    {
        return $this->credit_organization;
    }

    /**
     * Set events_organizer
     *
     * @param boolean $eventsOrganizer
     * @return Maker
     */
    public function setEventsOrganizer($eventsOrganizer)
    {
        $this->events_organizer = $eventsOrganizer;
    
        return $this;
    }

    /**
     * Get events_organizer
     *
     * @return boolean 
     */
    public function getEventsOrganizer()
    {
        return $this->events_organizer;
    }

    /**
     * Set entrance_exams_organizer
     *
     * @param boolean $entranceExamsOrganizer
     * @return Maker
     */
    public function setEntranceExamsOrganizer($entranceExamsOrganizer)
    {
        $this->entrance_exams_organizer = $entranceExamsOrganizer;
    
        return $this;
    }

    /**
     * Get entrance_exams_organizer
     *
     * @return boolean 
     */
    public function getEntranceExamsOrganizer()
    {
        return $this->entrance_exams_organizer;
    }

    /**
     * Set recruitment_agency
     *
     * @param boolean $recruitmentAgency
     * @return Maker
     */
    public function setRecruitmentAgency($recruitmentAgency)
    {
        $this->recruitment_agency = $recruitmentAgency;
    
        return $this;
    }

    /**
     * Get recruitment_agency
     *
     * @return boolean 
     */
    public function getRecruitmentAgency()
    {
        return $this->recruitment_agency;
    }

    /**
     * Set has_association
     *
     * @param boolean $hasAssociation
     * @return Maker
     */
    public function setHasAssociation($hasAssociation)
    {
        $this->has_association = $hasAssociation;
    
        return $this;
    }

    /**
     * Get has_association
     *
     * @return boolean 
     */
    public function getHasAssociation()
    {
        return $this->has_association;
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Calendar;


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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Questionnaire;


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
    /**
     * @var string
     */
    private $address;

    /**
     * Get QuestionnaireUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionnaireBy($params = array())
    {
        $quests = array();
        foreach($this->Questionnaire as $k => $quest){
            if(array_key_exists('by_status', $params)){
                if($params['by_status'] == 'published'){
                    if($quest->getPublished() == 1 && $quest->getApprobation() == 1 && $quest->getIsEndClose() != 1){
                        $quests[$k] = $quest;
                    }
                }elseif($params['by_status'] == 'creation'){
                    if($quest->getPublished() != 1 && $quest->getApprobation() != 1 && $quest->getIsEndClose() != 1){
                        $quests[$k] = $quest;
                    }
                }elseif($params['by_status'] == 'need_validate'){
                    if($quest->getPublished() == 1 && $quest->getApprobation() != 1 && $quest->getIsEndClose() != 1){
                        $quests[$k] = $quest;
                    }
                }elseif($params['by_status'] == 'unpublished'){
                    if($quest->getPublished() != 1 && $quest->getApprobation() == 1 && $quest->getIsEndClose() != 1){
                        $quests[$k] = $quest;
                    }
                }elseif($params['by_status'] == 'closed'){
                    if($quest->getIsEndClose() == 1){
                        $quests[$k] = $quest;
                    }
                }
            }
        }
        return $quests;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Maker
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MakerInvitationAdministrator;


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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MakerNote;


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
     * @var array
     */
    private $status;


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
     * @var integer
     */
    private $credit;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $CreditHistory;


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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Advert;


    /**
     * Add Advert
     *
     * @param \VM\MakerBundle\Entity\Maker $advert
     * @return Maker
     */
    public function addAdvert(\VM\MakerBundle\Entity\Maker $advert)
    {
        $this->Advert[] = $advert;
    
        return $this;
    }

    /**
     * Remove Advert
     *
     * @param \VM\MakerBundle\Entity\Maker $advert
     */
    public function removeAdvert(\VM\MakerBundle\Entity\Maker $advert)
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
}