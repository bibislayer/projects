<?php

namespace VM\MakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MakerAdministrator
 */
class MakerAdministrator
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var array
     */
    private $roles;

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
     * Set roles
     *
     * @param array $roles
     * @return MakerAdministrator
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
     * Set Maker
     *
     * @param \VM\MakerBundle\Entity\Maker $maker
     * @return MakerAdministrator
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
     * @return MakerAdministrator
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