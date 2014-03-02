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
    private $Video;


    /**
     * Add Video
     *
     * @param \VM\VideoBundle\Entity\Video $video
     * @return User
     */
    public function addVideo(\VM\VideoBundle\Entity\Video $video)
    {
        $this->Video[] = $video;
    
        return $this;
    }

    /**
     * Remove Video
     *
     * @param \VM\VideoBundle\Entity\Video $video
     */
    public function removeVideo(\VM\VideoBundle\Entity\Video $video)
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
    private $RecordingSession;


    /**
     * Add RecordingSession
     *
     * @param \VM\RecordingSessionBundle\Entity\RecordingSession $recordingSession
     * @return User
     */
    public function addRecordingSession(\VM\RecordingSessionBundle\Entity\RecordingSession $recordingSession)
    {
        $this->RecordingSession[] = $recordingSession;
    
        return $this;
    }

    /**
     * Remove RecordingSession
     *
     * @param \VM\RecordingSessionBundle\Entity\RecordingSession $recordingSession
     */
    public function removeRecordingSession(\VM\RecordingSessionBundle\Entity\RecordingSession $recordingSession)
    {
        $this->RecordingSession->removeElement($recordingSession);
    }

    /**
     * Get RecordingSession
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecordingSession()
    {
        return $this->RecordingSession;
    }
}