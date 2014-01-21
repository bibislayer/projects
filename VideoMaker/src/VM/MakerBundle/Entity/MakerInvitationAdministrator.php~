<?php

namespace VM\MakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MakerInvitationAdministrator
 */
class MakerInvitationAdministrator
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var string
     */
    private $confirmation_token;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \VM\MakerBundle\Entity\Maker
     */
    private $Maker;

    /**
     * @var \VM\UserBundle\Entity\User
     */
    private $User;


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
     * Set email
     *
     * @param string $email
     * @return MakerInvitationAdministrator
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return MakerInvitationAdministrator
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    
        return $this;
    }

    /**
     * Get roles
     *
     * @return array 
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set confirmation_token
     *
     * @param string $confirmationToken
     * @return MakerInvitationAdministrator
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmation_token = $confirmationToken;
    
        return $this;
    }

    /**
     * Get confirmation_token
     *
     * @return string 
     */
    public function getConfirmationToken()
    {
        return $this->confirmation_token;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return MakerInvitationAdministrator
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
     * @return MakerInvitationAdministrator
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
     * Set Maker
     *
     * @param \VM\MakerBundle\Entity\Maker $maker
     * @return MakerInvitationAdministrator
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

    /**
     * Set User
     *
     * @param \VM\UserBundle\Entity\User $user
     * @return MakerInvitationAdministrator
     */
    public function setUser(\VM\UserBundle\Entity\User $user = null)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get User
     *
     * @return \VM\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->User;
    }
}