<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Questionnaire
 */
class Questionnaire
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
     * @var \DateTime
     */
    private $date_start;

    /**
     * @var \DateTime
     */
    private $date_end;

    /**
     * @var boolean
     */
    private $is_end_close;

    /**
     * @var string
     */
    private $text_introduction;

    /**
     * @var string
     */
    private $text_presentation;

    /**
     * @var boolean
     */
    private $payment;

    /**
     * @var string
     */
    private $text_payment;

    /**
     * @var float
     */
    private $payment_amount_before;

    /**
     * @var float
     */
    private $payment_amount_after;

    /**
     * @var float
     */
    private $payment_vat;

    /**
     * @var string
     */
    private $mail_invitation;

    /**
     * @var string
     */
    private $mail_accepted;

    /**
     * @var string
     */
    private $mail_refused;

    /**
     * @var boolean
     */
    private $published;

    /**
     * @var \DateTime
     */
    private $published_at;

    /**
     * @var boolean
     */
    private $approbation;

    /**
     * @var boolean
     */
    private $is_model;

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
    private $QuestionnaireAdministrator;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireUser;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireRestriction;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireElement;

    /**
     * @var \VM\EnterpriseBundle\Entity\Enterprise
     */
    private $Enterprise;

    /**
     * @var \VM\StandardBundle\Entity\StdQuestionnaireType
     */
    private $StdQuestionnaireType;

    /**
     * @var \VM\QuestionnaireBundle\Entity\Help
     */
    private $Help;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireCategory;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->QuestionnaireAdministrator = new \Doctrine\Common\Collections\ArrayCollection();
        $this->QuestionnaireUser = new \Doctrine\Common\Collections\ArrayCollection();
        $this->QuestionnaireRestriction = new \Doctrine\Common\Collections\ArrayCollection();
        $this->QuestionnaireElement = new \Doctrine\Common\Collections\ArrayCollection();
        $this->QuestionnaireCategory = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Questionnaire
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
     * Set date_start
     *
     * @param \DateTime $dateStart
     * @return Questionnaire
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
     * @return Questionnaire
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
     * Set is_end_close
     *
     * @param boolean $isEndClose
     * @return Questionnaire
     */
    public function setIsEndClose($isEndClose)
    {
        $this->is_end_close = $isEndClose;
    
        return $this;
    }

    /**
     * Get is_end_close
     *
     * @return boolean 
     */
    public function getIsEndClose()
    {
        return $this->is_end_close;
    }

    /**
     * Set text_introduction
     *
     * @param string $textIntroduction
     * @return Questionnaire
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
     * @return Questionnaire
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
     * Set payment
     *
     * @param boolean $payment
     * @return Questionnaire
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    
        return $this;
    }

    /**
     * Get payment
     *
     * @return boolean 
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set text_payment
     *
     * @param string $textPayment
     * @return Questionnaire
     */
    public function setTextPayment($textPayment)
    {
        $this->text_payment = $textPayment;
    
        return $this;
    }

    /**
     * Get text_payment
     *
     * @return string 
     */
    public function getTextPayment()
    {
        return $this->text_payment;
    }

    /**
     * Set payment_amount_before
     *
     * @param float $paymentAmountBefore
     * @return Questionnaire
     */
    public function setPaymentAmountBefore($paymentAmountBefore)
    {
        $this->payment_amount_before = $paymentAmountBefore;
    
        return $this;
    }

    /**
     * Get payment_amount_before
     *
     * @return float 
     */
    public function getPaymentAmountBefore()
    {
        return $this->payment_amount_before;
    }

    /**
     * Set payment_amount_after
     *
     * @param float $paymentAmountAfter
     * @return Questionnaire
     */
    public function setPaymentAmountAfter($paymentAmountAfter)
    {
        $this->payment_amount_after = $paymentAmountAfter;
    
        return $this;
    }

    /**
     * Get payment_amount_after
     *
     * @return float 
     */
    public function getPaymentAmountAfter()
    {
        return $this->payment_amount_after;
    }

    /**
     * Set payment_vat
     *
     * @param float $paymentVat
     * @return Questionnaire
     */
    public function setPaymentVat($paymentVat)
    {
        $this->payment_vat = $paymentVat;
    
        return $this;
    }

    /**
     * Get payment_vat
     *
     * @return float 
     */
    public function getPaymentVat()
    {
        return $this->payment_vat;
    }

    /**
     * Set mail_invitation
     *
     * @param string $mailInvitation
     * @return Questionnaire
     */
    public function setMailInvitation($mailInvitation)
    {
        $this->mail_invitation = $mailInvitation;
    
        return $this;
    }

    /**
     * Get mail_invitation
     *
     * @return string 
     */
    public function getMailInvitation()
    {
        return $this->mail_invitation;
    }

    /**
     * Set mail_accepted
     *
     * @param string $mailAccepted
     * @return Questionnaire
     */
    public function setMailAccepted($mailAccepted)
    {
        $this->mail_accepted = $mailAccepted;
    
        return $this;
    }

    /**
     * Get mail_accepted
     *
     * @return string 
     */
    public function getMailAccepted()
    {
        return $this->mail_accepted;
    }

    /**
     * Set mail_refused
     *
     * @param string $mailRefused
     * @return Questionnaire
     */
    public function setMailRefused($mailRefused)
    {
        $this->mail_refused = $mailRefused;
    
        return $this;
    }

    /**
     * Get mail_refused
     *
     * @return string 
     */
    public function getMailRefused()
    {
        return $this->mail_refused;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Questionnaire
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set published_at
     *
     * @param \DateTime $publishedAt
     * @return Questionnaire
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
     * Set approbation
     *
     * @param boolean $approbation
     * @return Questionnaire
     */
    public function setApprobation($approbation)
    {
        $this->approbation = $approbation;
    
        return $this;
    }

    /**
     * Get approbation
     *
     * @return boolean 
     */
    public function getApprobation()
    {
        return $this->approbation;
    }

    /**
     * Set is_model
     *
     * @param boolean $isModel
     * @return Questionnaire
     */
    public function setIsModel($isModel)
    {
        $this->is_model = $isModel;
    
        return $this;
    }

    /**
     * Get is_model
     *
     * @return boolean 
     */
    public function getIsModel()
    {
        return $this->is_model;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Questionnaire
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
     * @return Questionnaire
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
     * @return Questionnaire
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
     * Add QuestionnaireAdministrator
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireAdministrator $questionnaireAdministrator
     * @return Questionnaire
     */
    public function addQuestionnaireAdministrator(\VM\QuestionnaireBundle\Entity\QuestionnaireAdministrator $questionnaireAdministrator)
    {
        $this->QuestionnaireAdministrator[] = $questionnaireAdministrator;
    
        return $this;
    }

    /**
     * Remove QuestionnaireAdministrator
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireAdministrator $questionnaireAdministrator
     */
    public function removeQuestionnaireAdministrator(\VM\QuestionnaireBundle\Entity\QuestionnaireAdministrator $questionnaireAdministrator)
    {
        $this->QuestionnaireAdministrator->removeElement($questionnaireAdministrator);
    }

    /**
     * Get QuestionnaireAdministrator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireAdministrator()
    {
        return $this->QuestionnaireAdministrator;
    }

    /**
     * Add QuestionnaireUser
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser
     * @return Questionnaire
     */
    public function addQuestionnaireUser(\VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser)
    {
        $this->QuestionnaireUser[] = $questionnaireUser;
    
        return $this;
    }

    /**
     * Remove QuestionnaireUser
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser
     */
    public function removeQuestionnaireUser(\VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser)
    {
        $this->QuestionnaireUser->removeElement($questionnaireUser);
    }

    /**
     * Get QuestionnaireUser
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireUser()
    {
        return $this->QuestionnaireUser;
    }

    /**
     * Get QuestionnaireUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestionnaireUserBy($params = array())
    {
        $users = array();
        foreach($this->QuestionnaireUser as $k => $user){
            if(array_key_exists('by_status', $params)){
                if($params['by_status'] == 'accepted'){
                    if($user->getStatus() == 1 ){
                        $users[$k] = $user;
                    }
                }
                if($params['by_status'] == 'refused'){
                    if($user->getStatus() == 2 ){
                        $users[$k] = $user;
                    }
                }
                if($params['by_status'] == 'test'){
                    if($user->getStatus() == 3 ){
                        $users[$k] = $user;
                    }
                }
                if($params['by_status'] == 'no-test'){
                    if($user->getStatus() != 3 ){
                        $users[$k] = $user;
                    }
                }
                if($params['by_status'] == 'new'){
                    if($user->getStatus() == 0 || !$user->getStatus()){
                        $users[$k] = $user;
                    }
                }
            }
        }
        return $users;
    }

    /**
     * Add QuestionnaireRestriction
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireRestriction $questionnaireRestriction
     * @return Questionnaire
     */
    public function addQuestionnaireRestriction(\VM\QuestionnaireBundle\Entity\QuestionnaireRestriction $questionnaireRestriction)
    {
        $this->QuestionnaireRestriction[] = $questionnaireRestriction;
    
        return $this;
    }

    /**
     * Remove QuestionnaireRestriction
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireRestriction $questionnaireRestriction
     */
    public function removeQuestionnaireRestriction(\VM\QuestionnaireBundle\Entity\QuestionnaireRestriction $questionnaireRestriction)
    {
        $this->QuestionnaireRestriction->removeElement($questionnaireRestriction);
    }

    /**
     * Get QuestionnaireRestriction
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireRestriction()
    {
        return $this->QuestionnaireRestriction;
    }

    /**
     * Add QuestionnaireElement
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement
     * @return Questionnaire
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

    /**
     * Get QuestionnaireElement
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireElement()
    {
        return $this->QuestionnaireElement;
    }

    /**
     * Set Enterprise
     *
     * @param \VM\EnterpriseBundle\Entity\Enterprise $enterprise
     * @return Questionnaire
     */
    public function setEnterprise(\VM\EnterpriseBundle\Entity\Enterprise $enterprise = null)
    {
        $this->Enterprise = $enterprise;
    
        return $this;
    }

    /**
     * Get Enterprise
     *
     * @return \VM\EnterpriseBundle\Entity\Enterprise 
     */
    public function getEnterprise()
    {
        return $this->Enterprise;
    }

    /**
     * Set StdQuestionnaireType
     *
     * @param \VM\StandardBundle\Entity\StdQuestionnaireType $stdQuestionnaireType
     * @return Questionnaire
     */
    public function setStdQuestionnaireType(\VM\StandardBundle\Entity\StdQuestionnaireType $stdQuestionnaireType = null)
    {
        $this->StdQuestionnaireType = $stdQuestionnaireType;
    
        return $this;
    }

    /**
     * Get StdQuestionnaireType
     *
     * @return \VM\StandardBundle\Entity\StdQuestionnaireType 
     */
    public function getStdQuestionnaireType()
    {
        return $this->StdQuestionnaireType;
    }

    /**
     * Set Help
     *
     * @param \VM\QuestionnaireBundle\Entity\Help $help
     * @return Questionnaire
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
     * Add QuestionnaireCategory
     *
     * @param \VM\StandardBundle\Entity\StdCategory $questionnaireCategory
     * @return Questionnaire
     */
    public function addQuestionnaireCategory(\VM\StandardBundle\Entity\StdCategory $questionnaireCategory)
    {
        $this->QuestionnaireCategory[] = $questionnaireCategory;
    
        return $this;
    }

    /**
     * Remove QuestionnaireCategory
     *
     * @param \VM\StandardBundle\Entity\StdCategory $questionnaireCategory
     */
    public function removeQuestionnaireCategory(\VM\StandardBundle\Entity\StdCategory $questionnaireCategory)
    {
        $this->QuestionnaireCategory->removeElement($questionnaireCategory);
    }

    /**
     * Get QuestionnaireCategory
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireCategory()
    {
        return $this->QuestionnaireCategory;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $AdvertQuestionnaire;


    /**
     * Add AdvertQuestionnaire
     *
     * @param \VM\EnterpriseBundle\Entity\AdvertQuestionnaire $advertQuestionnaire
     * @return Questionnaire
     */
    public function addAdvertQuestionnaire(\VM\EnterpriseBundle\Entity\AdvertQuestionnaire $advertQuestionnaire)
    {
        $this->AdvertQuestionnaire[] = $advertQuestionnaire;
    
        return $this;
    }

    /**
     * Remove AdvertQuestionnaire
     *
     * @param \VM\EnterpriseBundle\Entity\AdvertQuestionnaire $advertQuestionnaire
     */
    public function removeAdvertQuestionnaire(\VM\EnterpriseBundle\Entity\AdvertQuestionnaire $advertQuestionnaire)
    {
        $this->AdvertQuestionnaire->removeElement($advertQuestionnaire);
    }

    /**
     * Get AdvertQuestionnaire
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdvertQuestionnaire()
    {
        return $this->AdvertQuestionnaire;
    }
    /**
     * @var boolean
     */
    private $anonymous;


    /**
     * Set anonymous
     *
     * @param boolean $anonymous
     * @return Questionnaire
     */
    public function setAnonymous($anonymous)
    {
        $this->anonymous = $anonymous;
    
        return $this;
    }

    /**
     * Get anonymous
     *
     * @return boolean 
     */
    public function getAnonymous()
    {
        return $this->anonymous;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $CreditHistory;


    /**
     * Add CreditHistory
     *
     * @param \VM\CustomerBundle\Entity\CreditHistory $creditHistory
     * @return Questionnaire
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
     * @var \VM\MakerBundle\Entity\Maker
     */
    private $Maker;


    /**
     * Set Maker
     *
     * @param \VM\MakerBundle\Entity\Maker $maker
     * @return Questionnaire
     */
    public function setMaker(\VM\MakerBundle\Entity\Maker $maker = null)
    {
        $this->Maker = $maker;
    
        return $this;
    }

    /**
     * Get Maker
     *
     * @return \VM\MakerBundle\Entity\Maker 
     */
    public function getMaker()
    {
        return $this->Maker;
    }
}