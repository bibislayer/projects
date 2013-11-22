<?php

namespace FP\UserBundle\Entity;

use FP\UserBundle\Entity\UserProfile;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 */
class User extends BaseUser {

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $groups;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * Add users
     *
     * @param \FP\UserBundle\Entity\User $users
     * @return User
     */
    public function addUser(\FP\UserBundle\Entity\User $users) {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \FP\UserBundle\Entity\User $users
     */
    public function removeUser(\FP\UserBundle\Entity\User $users) {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $enterprises;

    //$this->Enterprises = new \Doctrine\Common\Collections\ArrayCollection();
    /**
     * Add Enterprise
     *
     * @param \FP\EnterpriseBundle\Entity\Enterprise $enterprise
     * @return User
     */
    public function addEnterprise(\FP\EnterpriseBundle\Entity\Enterprise $enterprise) {
        $this->enterprises[] = $enterprise;

        return $this;
    }

    /**
     * Remove Enterprise
     *
     * @param \FP\EnterpriseBundle\Entity\Enterprise $enterprise
     */
    public function removeEnterprise(\FP\EnterpriseBundle\Entity\Enterprise $enterprise) {
        $this->enterprises->removeElement($enterprise);
    }

    /**
     * Get Enterprises
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnterprises() {
        return $this->enterprises;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookId", type="string", length=255)
     */
    protected $facebookId;

    public function serialize() {
        return serialize(array($this->facebookId, parent::serialize()));
    }

    public function unserialize($data) {
        list($this->facebookId, $parentData) = unserialize($data);
        parent::unserialize($parentData);
    }

    /**
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    /**
     * Get the full name of the user (first + last name)
     * @return string
     */
    public function getFullName() {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }

    /**
     * @param string $facebookId
     * @return void
     */
    public function setFacebookId($facebookId) {
        $this->facebookId = $facebookId;
        $this->setUsername($facebookId);
    }

    /**
     * @return string
     */
    public function getFacebookId() {
        return $this->facebookId;
    }

    /**
     * @param Array
     */
    public function setData($data, $identity) {
        if (!$this->getUserProfile())
            $profile = new UserProfile();
        else
            $profile = $this->getUserProfile();
        if ($identity == 'f') {
            if (isset($data['id'])) {
                $this->setFacebookId($data['id']);
                $this->addRole('ROLE_FACEBOOK');
            }
            if (isset($data['username']))
                $this->setUsername($data['username']);
            if (isset($data['gender']))
                $profile->setGender($data['gender']);
            if (isset($data['birthday']))
                $profile->setBirthday(\DateTime::createFromFormat('m/d/Y', $data["birthday"]));
            if (isset($data['first_name']))
                $profile->setFirstname($data['first_name']);
            if (isset($data['last_name']))
                $profile->setLastname($data['last_name']);
            if (isset($data['email']))
                $this->setEmail($data['email']);
        }else if($identity == 'g'){
            if (isset($data['namePerson/first']))
                $profile->setFirstname($data['namePerson/first']);
            if (isset($data['namePerson/last']))
                $profile->setLastname($data['namePerson/last']);
            if (isset($data['contact/email']))
                $this->setEmail($data['contact/email']);
            $this->setUsername('none');
        }
        $profile->setUser($this);

        $this->setUserProfile($profile);
    }

    /**
     * @var \FP\UserBundle\Entity\UserPofile
     */
    private $UserPofile;

    /**
     * @var \FP\UserBundle\Entity\UserProfile
     */
    private $UserProfile;

    /**
     * Set UserProfile
     *
     * @param \FP\UserBundle\Entity\UserProfile $userProfile
     * @return User
     */
    public function setUserProfile(\FP\UserBundle\Entity\UserProfile $userProfile = null) {
        $this->UserProfile = $userProfile;

        return $this;
    }

    /**
     * Get UserProfile
     *
     * @return \FP\UserBundle\Entity\UserProfile 
     */
    public function getUserProfile() {
        return $this->UserProfile;
    }

    /**
     * @var string
     */
    private $email_new;


    /**
     * Set email_new
     *
     * @param string $emailNew
     * @return User
     */
    public function setEmailNew($emailNew)
    {
        $this->email_new = $emailNew;
    
        return $this;
    }

    /**
     * Get email_new
     *
     * @return string 
     */
    public function getEmailNew()
    {
        return $this->email_new;
    }
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireUser;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $EnterpriseAdministrator;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $QuestionnaireAdministrator;


    /**
     * Add QuestionnaireUser
     *
     * @param \FP\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser
     * @return User
     */
    public function addQuestionnaireUser(\FP\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser)
    {
        $this->QuestionnaireUser[] = $questionnaireUser;
    
        return $this;
    }

    /**
     * Remove QuestionnaireUser
     *
     * @param \FP\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser
     */
    public function removeQuestionnaireUser(\FP\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser)
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
     * Add EnterpriseAdministrator
     *
     * @param \FP\EnterpriseBundle\Entity\Enterprise $enterpriseAdministrator
     * @return User
     */
    public function addEnterpriseAdministrator(\FP\EnterpriseBundle\Entity\Enterprise $enterpriseAdministrator)
    {
        $this->EnterpriseAdministrator[] = $enterpriseAdministrator;
    
        return $this;
    }

    /**
     * Remove EnterpriseAdministrator
     *
     * @param \FP\EnterpriseBundle\Entity\Enterprise $enterpriseAdministrator
     */
    public function removeEnterpriseAdministrator(\FP\EnterpriseBundle\Entity\Enterprise $enterpriseAdministrator)
    {
        $this->EnterpriseAdministrator->removeElement($enterpriseAdministrator);
    }

    /**
     * Get EnterpriseAdministrator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnterpriseAdministrator()
    {
        return $this->EnterpriseAdministrator;
    }

    /**
     * Add QuestionnaireAdministrator
     *
     * @param \FP\QuestionnaireBundle\Entity\Questionnaire $questionnaireAdministrator
     * @return User
     */
    public function addQuestionnaireAdministrator(\FP\QuestionnaireBundle\Entity\Questionnaire $questionnaireAdministrator)
    {
        $this->QuestionnaireAdministrator[] = $questionnaireAdministrator;
    
        return $this;
    }

    /**
     * Remove QuestionnaireAdministrator
     *
     * @param \FP\QuestionnaireBundle\Entity\Questionnaire $questionnaireAdministrator
     */
    public function removeQuestionnaireAdministrator(\FP\QuestionnaireBundle\Entity\Questionnaire $questionnaireAdministrator)
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Calendar;


    /**
     * Add Calendar
     *
     * @param \FP\CalendarBundle\Entity\Calendar $calendar
     * @return User
     */
    public function addCalendar(\FP\CalendarBundle\Entity\Calendar $calendar)
    {
        $this->Calendar[] = $calendar;
    
        return $this;
    }

    /**
     * Remove Calendar
     *
     * @param \FP\CalendarBundle\Entity\Calendar $calendar
     */
    public function removeCalendar(\FP\CalendarBundle\Entity\Calendar $calendar)
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
    private $EnterpriseInvitationAdministrator;


    /**
     * Add EnterpriseInvitationAdministrator
     *
     * @param \FP\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $enterpriseInvitationAdministrator
     * @return User
     */
    public function addEnterpriseInvitationAdministrator(\FP\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $enterpriseInvitationAdministrator)
    {
        $this->EnterpriseInvitationAdministrator[] = $enterpriseInvitationAdministrator;
    
        return $this;
    }

    /**
     * Remove EnterpriseInvitationAdministrator
     *
     * @param \FP\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $enterpriseInvitationAdministrator
     */
    public function removeEnterpriseInvitationAdministrator(\FP\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $enterpriseInvitationAdministrator)
    {
        $this->EnterpriseInvitationAdministrator->removeElement($enterpriseInvitationAdministrator);
    }

    /**
     * Get EnterpriseInvitationAdministrator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnterpriseInvitationAdministrator()
    {
        return $this->EnterpriseInvitationAdministrator;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Feedback;


    /**
     * Add Feedback
     *
     * @param \FP\FeedbackBundle\Entity\Feedback $feedback
     * @return User
     */
    public function addFeedback(\FP\FeedbackBundle\Entity\Feedback $feedback)
    {
        $this->Feedback[] = $feedback;
    
        return $this;
    }

    /**
     * Remove Feedback
     *
     * @param \FP\FeedbackBundle\Entity\Feedback $feedback
     */
    public function removeFeedback(\FP\FeedbackBundle\Entity\Feedback $feedback)
    {
        $this->Feedback->removeElement($feedback);
    }

    /**
     * Get Feedback
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeedback()
    {
        return $this->Feedback;
    }
}