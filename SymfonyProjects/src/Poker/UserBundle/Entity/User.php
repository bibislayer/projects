<?php

namespace Poker\UserBundle\Entity;

use Poker\UserBundle\Entity\UserProfile;
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
     * @param \Poker\UserBundle\Entity\User $users
     * @return User
     */
    public function addUser(\Poker\UserBundle\Entity\User $users) {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Poker\UserBundle\Entity\User $users
     */
    public function removeUser(\Poker\UserBundle\Entity\User $users) {
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
     * @var \Poker\UserBundle\Entity\UserPofile
     */
    private $UserPofile;

    /**
     * @var \Poker\UserBundle\Entity\UserProfile
     */
    private $UserProfile;

    /**
     * Set UserProfile
     *
     * @param \Poker\UserBundle\Entity\UserProfile $userProfile
     * @return User
     */
    public function setUserProfile(\Poker\UserBundle\Entity\UserProfile $userProfile = null) {
        $this->UserProfile = $userProfile;

        return $this;
    }

    /**
     * Get UserProfile
     *
     * @return \Poker\UserBundle\Entity\UserProfile 
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
    private $PokerTable;


    /**
     * Add PokerTable
     *
     * @param \Poker\PokerTableBundle\Entity\PokerTable $pokerTable
     * @return User
     */
    public function addPokerTable(\Poker\PokerTableBundle\Entity\PokerTable $pokerTable)
    {
        $this->PokerTable[] = $pokerTable;
    
        return $this;
    }

    /**
     * Remove PokerTable
     *
     * @param \Poker\PokerTableBundle\Entity\PokerTable $pokerTable
     */
    public function removePokerTable(\Poker\PokerTableBundle\Entity\PokerTable $pokerTable)
    {
        $this->PokerTable->removeElement($pokerTable);
    }

    /**
     * Get PokerTable
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPokerTable()
    {
        return $this->PokerTable;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $PokerUser;


    /**
     * Add PokerUser
     *
     * @param \Poker\PokerTableBundle\Entity\PokerUser $pokerUser
     * @return User
     */
    public function addPokerUser(\Poker\PokerTableBundle\Entity\PokerUser $pokerUser)
    {
        $this->PokerUser[] = $pokerUser;
    
        return $this;
    }

    /**
     * Remove PokerUser
     *
     * @param \Poker\PokerTableBundle\Entity\PokerUser $pokerUser
     */
    public function removePokerUser(\Poker\PokerTableBundle\Entity\PokerUser $pokerUser)
    {
        $this->PokerUser->removeElement($pokerUser);
    }

    /**
     * Get PokerUser
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPokerUser()
    {
        return $this->PokerUser;
    }
}