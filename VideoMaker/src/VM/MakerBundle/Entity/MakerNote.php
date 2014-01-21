<?php

namespace VM\MakerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MakerNote
 */
class MakerNote
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $note;

    /**
     * @var \DateTime
     */
    private $date_recall;

    /**
     * @var boolean
     */
    private $is_main;

    /**
     * @var boolean
     */
    private $is_close;

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
     * Set type
     *
     * @param string $type
     * @return MakerNote
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return MakerNote
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set date_recall
     *
     * @param \DateTime $dateRecall
     * @return MakerNote
     */
    public function setDateRecall($dateRecall)
    {
        $this->date_recall = $dateRecall;
    
        return $this;
    }

    /**
     * Get date_recall
     *
     * @return \DateTime 
     */
    public function getDateRecall()
    {
        return $this->date_recall;
    }

    /**
     * Set is_main
     *
     * @param boolean $isMain
     * @return MakerNote
     */
    public function setIsMain($isMain)
    {
        $this->is_main = $isMain;
    
        return $this;
    }

    /**
     * Get is_main
     *
     * @return boolean 
     */
    public function getIsMain()
    {
        return $this->is_main;
    }

    /**
     * Set is_close
     *
     * @param boolean $isClose
     * @return MakerNote
     */
    public function setIsClose($isClose)
    {
        $this->is_close = $isClose;
    
        return $this;
    }

    /**
     * Get is_close
     *
     * @return boolean 
     */
    public function getIsClose()
    {
        return $this->is_close;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return MakerNote
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
     * @return MakerNote
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
     * @return MakerNote
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
     * @return MakerNote
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