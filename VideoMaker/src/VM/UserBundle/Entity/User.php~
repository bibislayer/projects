<?php

namespace VM\UserBundle\Entity;

use VM\UserBundle\Entity\UserProfile;
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
     * @param \VM\UserBundle\Entity\User $users
     * @return User
     */
    public function addUser(\VM\UserBundle\Entity\User $users) {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \VM\UserBundle\Entity\User $users
     */
    public function removeUser(\VM\UserBundle\Entity\User $users) {
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
     * @param \VM\EnterpriseBundle\Entity\Enterprise $enterprise
     * @return User
     */
    public function addEnterprise(\VM\EnterpriseBundle\Entity\Enterprise $enterprise) {
        $this->enterprises[] = $enterprise;

        return $this;
    }

    /**
     * Remove Enterprise
     *
     * @param \VM\EnterpriseBundle\Entity\Enterprise $enterprise
     */
    public function removeEnterprise(\VM\EnterpriseBundle\Entity\Enterprise $enterprise) {
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
        }else if ($identity == 'g') {
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
     * @var \VM\UserBundle\Entity\UserPofile
     */
    private $UserPofile;

    /**
     * @var \VM\UserBundle\Entity\UserProfile
     */
    private $UserProfile;

    /**
     * Set UserProfile
     *
     * @param \VM\UserBundle\Entity\UserProfile $userProfile
     * @return User
     */
    public function setUserProfile(\VM\UserBundle\Entity\UserProfile $userProfile = null) {
        $this->UserProfile = $userProfile;

        return $this;
    }

    /**
     * Get UserProfile
     *
     * @return \VM\UserBundle\Entity\UserProfile 
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
    public function setEmailNew($emailNew) {
        $this->email_new = $emailNew;

        return $this;
    }

    /**
     * Get email_new
     *
     * @return string 
     */
    public function getEmailNew() {
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
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser
     * @return User
     */
    public function addQuestionnaireUser(\VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser) {
        $this->QuestionnaireUser[] = $questionnaireUser;

        return $this;
    }

    /**
     * Remove QuestionnaireUser
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser
     */
    public function removeQuestionnaireUser(\VM\QuestionnaireBundle\Entity\QuestionnaireUser $questionnaireUser) {
        $this->QuestionnaireUser->removeElement($questionnaireUser);
    }

    /**
     * Get QuestionnaireUser
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireUser() {
        return $this->QuestionnaireUser;
    }

    /**
     * Add EnterpriseAdministrator
     *
     * @param \VM\EnterpriseBundle\Entity\Enterprise $enterpriseAdministrator
     * @return User
     */
    public function addEnterpriseAdministrator(\VM\EnterpriseBundle\Entity\Enterprise $enterpriseAdministrator) {
        $this->EnterpriseAdministrator[] = $enterpriseAdministrator;

        return $this;
    }

    /**
     * Remove EnterpriseAdministrator
     *
     * @param \VM\EnterpriseBundle\Entity\Enterprise $enterpriseAdministrator
     */
    public function removeEnterpriseAdministrator(\VM\EnterpriseBundle\Entity\Enterprise $enterpriseAdministrator) {
        $this->EnterpriseAdministrator->removeElement($enterpriseAdministrator);
    }

    /**
     * Get EnterpriseAdministrator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnterpriseAdministrator() {
        return $this->EnterpriseAdministrator;
    }

    /**
     * Add QuestionnaireAdministrator
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaireAdministrator
     * @return User
     */
    public function addQuestionnaireAdministrator(\VM\QuestionnaireBundle\Entity\Questionnaire $questionnaireAdministrator) {
        $this->QuestionnaireAdministrator[] = $questionnaireAdministrator;

        return $this;
    }

    /**
     * Remove QuestionnaireAdministrator
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaireAdministrator
     */
    public function removeQuestionnaireAdministrator(\VM\QuestionnaireBundle\Entity\Questionnaire $questionnaireAdministrator) {
        $this->QuestionnaireAdministrator->removeElement($questionnaireAdministrator);
    }

    /**
     * Get QuestionnaireAdministrator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestionnaireAdministrator() {
        return $this->QuestionnaireAdministrator;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Calendar;

    /**
     * Add Calendar
     *
     * @param \VM\CalendarBundle\Entity\Calendar $calendar
     * @return User
     */
    public function addCalendar(\VM\CalendarBundle\Entity\Calendar $calendar) {
        $this->Calendar[] = $calendar;

        return $this;
    }

    /**
     * Remove Calendar
     *
     * @param \VM\CalendarBundle\Entity\Calendar $calendar
     */
    public function removeCalendar(\VM\CalendarBundle\Entity\Calendar $calendar) {
        $this->Calendar->removeElement($calendar);
    }

    /**
     * Get Calendar
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCalendar() {
        return $this->Calendar;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $EnterpriseInvitationAdministrator;

    /**
     * Add EnterpriseInvitationAdministrator
     *
     * @param \VM\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $enterpriseInvitationAdministrator
     * @return User
     */
    public function addEnterpriseInvitationAdministrator(\VM\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $enterpriseInvitationAdministrator) {
        $this->EnterpriseInvitationAdministrator[] = $enterpriseInvitationAdministrator;

        return $this;
    }

    /**
     * Remove EnterpriseInvitationAdministrator
     *
     * @param \VM\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $enterpriseInvitationAdministrator
     */
    public function removeEnterpriseInvitationAdministrator(\VM\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $enterpriseInvitationAdministrator) {
        $this->EnterpriseInvitationAdministrator->removeElement($enterpriseInvitationAdministrator);
    }

    /**
     * Get EnterpriseInvitationAdministrator
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnterpriseInvitationAdministrator() {
        return $this->EnterpriseInvitationAdministrator;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Feedback;

    /**
     * Add Feedback
     *
     * @param \VM\FeedbackBundle\Entity\Feedback $feedback
     * @return User
     */
    public function addFeedback(\VM\FeedbackBundle\Entity\Feedback $feedback) {
        $this->Feedback[] = $feedback;

        return $this;
    }

    /**
     * Remove Feedback
     *
     * @param \VM\FeedbackBundle\Entity\Feedback $feedback
     */
    public function removeFeedback(\VM\FeedbackBundle\Entity\Feedback $feedback) {
        $this->Feedback->removeElement($feedback);
    }

    /**
     * Get Feedback
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFeedback() {
        return $this->Feedback;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $EnterpriseNote;

    /**
     * Add EnterpriseNote
     *
     * @param \VM\EnterpriseBundle\Entity\EnterpriseNote $enterpriseNote
     * @return User
     */
    public function addEnterpriseNote(\VM\EnterpriseBundle\Entity\EnterpriseNote $enterpriseNote) {
        $this->EnterpriseNote[] = $enterpriseNote;

        return $this;
    }

    /**
     * Remove EnterpriseNote
     *
     * @param \VM\EnterpriseBundle\Entity\EnterpriseNote $enterpriseNote
     */
    public function removeEnterpriseNote(\VM\EnterpriseBundle\Entity\EnterpriseNote $enterpriseNote) {
        $this->EnterpriseNote->removeElement($enterpriseNote);
    }

    /**
     * Get EnterpriseNote
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnterpriseNote() {
        return $this->EnterpriseNote;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Video;


    /**
     * Add Video
     *
     * @param \VM\UserBundle\Entity\Video $video
     * @return User
     */
    public function addVideo(\VM\UserBundle\Entity\Video $video)
    {
        $this->Video[] = $video;
    
        return $this;
    }

    /**
     * Remove Video
     *
     * @param \VM\UserBundle\Entity\Video $video
     */
    public function removeVideo(\VM\UserBundle\Entity\Video $video)
    {
        $this->Video->removeElement($video);
    }

    /**
     * Get Video
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVideo()
    {
        return $this->Video;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MakerAdministrator;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $MakerInvitationAdministrator;


    /**
     * Add MakerAdministrator
     *
     * @param \VM\EnterpriseBundle\Entity\EnterpriseAdministrator $makerAdministrator
     * @return User
     */
    public function addMakerAdministrator(\VM\EnterpriseBundle\Entity\EnterpriseAdministrator $makerAdministrator)
    {
        $this->MakerAdministrator[] = $makerAdministrator;
    
        return $this;
    }

    /**
     * Remove MakerAdministrator
     *
     * @param \VM\EnterpriseBundle\Entity\EnterpriseAdministrator $makerAdministrator
     */
    public function removeMakerAdministrator(\VM\EnterpriseBundle\Entity\EnterpriseAdministrator $makerAdministrator)
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
     * Add MakerInvitationAdministrator
     *
     * @param \VM\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $makerInvitationAdministrator
     * @return User
     */
    public function addMakerInvitationAdministrator(\VM\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $makerInvitationAdministrator)
    {
        $this->MakerInvitationAdministrator[] = $makerInvitationAdministrator;
    
        return $this;
    }

    /**
     * Remove MakerInvitationAdministrator
     *
     * @param \VM\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $makerInvitationAdministrator
     */
    public function removeMakerInvitationAdministrator(\VM\EnterpriseBundle\Entity\EnterpriseInvitationAdministrator $makerInvitationAdministrator)
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
    private $User;


    /**
     * Get User
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->User;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $Questionnaire;


    /**
     * Add Questionnaire
     *
     * @param \VM\QuestionnaireBundle\Entity\Questionnaire $questionnaire
     * @return User
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