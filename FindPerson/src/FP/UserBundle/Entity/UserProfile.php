<?php

namespace FP\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProfile
 */
class UserProfile
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $lastname;

    /**
     * @var \DateTime
     */
    private $birthday;

    /**
     * @var string
     */
    private $gender;

    /**
     * @var string
     */
    private $dernier_diplome;

    /**
     * @var string
     */
    private $post_actuel;

    /**
     * @var string
     */
    private $dernier_post;

    /**
     * @var string
     */
    private $entreprise;

    /**
     * @var string
     */
    private $telephone_fixe;

    /**
     * @var string
     */
    private $telephone_portable;

    /**
     * @var string
     */
    private $fax;

    /**
     * @var \FP\UserBundle\Entity\User
     */
    private $User;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $enterprises;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->enterprises = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstname
     *
     * @param string $firstname
     * @return UserProfile
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return UserProfile
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set birthday
     *
     * @param \DateTime $birthday
     * @return UserProfile
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    
        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return UserProfile
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
    
        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set dernier_diplome
     *
     * @param string $dernierDiplome
     * @return UserProfile
     */
    public function setDernierDiplome($dernierDiplome)
    {
        $this->dernier_diplome = $dernierDiplome;
    
        return $this;
    }

    /**
     * Get dernier_diplome
     *
     * @return string 
     */
    public function getDernierDiplome()
    {
        return $this->dernier_diplome;
    }

    /**
     * Set post_actuel
     *
     * @param string $postActuel
     * @return UserProfile
     */
    public function setPostActuel($postActuel)
    {
        $this->post_actuel = $postActuel;
    
        return $this;
    }

    /**
     * Get post_actuel
     *
     * @return string 
     */
    public function getPostActuel()
    {
        return $this->post_actuel;
    }

    /**
     * Set dernier_post
     *
     * @param string $dernierPost
     * @return UserProfile
     */
    public function setDernierPost($dernierPost)
    {
        $this->dernier_post = $dernierPost;
    
        return $this;
    }

    /**
     * Get dernier_post
     *
     * @return string 
     */
    public function getDernierPost()
    {
        return $this->dernier_post;
    }

    /**
     * Set entreprise
     *
     * @param string $entreprise
     * @return UserProfile
     */
    public function setEntreprise($entreprise)
    {
        $this->entreprise = $entreprise;
    
        return $this;
    }

    /**
     * Get entreprise
     *
     * @return string 
     */
    public function getEntreprise()
    {
        return $this->entreprise;
    }

    /**
     * Set telephone_fixe
     *
     * @param string $telephoneFixe
     * @return UserProfile
     */
    public function setTelephoneFixe($telephoneFixe)
    {
        $this->telephone_fixe = $telephoneFixe;
    
        return $this;
    }

    /**
     * Get telephone_fixe
     *
     * @return string 
     */
    public function getTelephoneFixe()
    {
        return $this->telephone_fixe;
    }

    /**
     * Set telephone_portable
     *
     * @param string $telephonePortable
     * @return UserProfile
     */
    public function setTelephonePortable($telephonePortable)
    {
        $this->telephone_portable = $telephonePortable;
    
        return $this;
    }

    /**
     * Get telephone_portable
     *
     * @return string 
     */
    public function getTelephonePortable()
    {
        return $this->telephone_portable;
    }

    /**
     * Set fax
     *
     * @param string $fax
     * @return UserProfile
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
     * Set User
     *
     * @param \FP\UserBundle\Entity\User $user
     * @return UserProfile
     */
    public function setUser(\FP\UserBundle\Entity\User $user = null)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get User
     *
     * @return \FP\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->User;
    }

    /**
     * Add enterprises
     *
     * @param \FP\EnterpriseBundle\Entity\Enterprise $enterprises
     * @return UserProfile
     */
    public function addEnterprise(\FP\EnterpriseBundle\Entity\Enterprise $enterprises)
    {
        $this->enterprises[] = $enterprises;
    
        return $this;
    }

    /**
     * Remove enterprises
     *
     * @param \FP\EnterpriseBundle\Entity\Enterprise $enterprises
     */
    public function removeEnterprise(\FP\EnterpriseBundle\Entity\Enterprise $enterprises)
    {
        $this->enterprises->removeElement($enterprises);
    }

    /**
     * Get enterprises
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEnterprises()
    {
        return $this->enterprises;
    }
}