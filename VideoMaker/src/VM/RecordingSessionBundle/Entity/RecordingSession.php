<?php

namespace VM\RecordingSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecordingSession
 */
class RecordingSession
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
    private $text_introduction;

    /**
     * @var string
     */
    private $text_presentation;

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
    private $RecordingSessionKeywordList;

    /**
     * @var \VM\UserBundle\Entity\User
     */
    private $User;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->RecordingSessionKeywordList = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->getName();
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
     * @return RecordingSession
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
     * Set text_introduction
     *
     * @param string $textIntroduction
     * @return RecordingSession
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
     * @return RecordingSession
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
     * Set published
     *
     * @param boolean $published
     * @return RecordingSession
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
     * @return RecordingSession
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
     * @return RecordingSession
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return RecordingSession
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
     * @return RecordingSession
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
     * @return RecordingSession
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
     * Add RecordingSessionKeywordList
     *
     * @param \VM\RecordingSessionBundle\Entity\RecordingSessionKeywordList $recordingSessionKeywordList
     * @return RecordingSession
     */
    public function addRecordingSessionKeywordList(\VM\RecordingSessionBundle\Entity\RecordingSessionKeywordList $recordingSessionKeywordList)
    {
        $this->RecordingSessionKeywordList[] = $recordingSessionKeywordList;
    
        return $this;
    }
    
    public function setRecordingSessionKeywordList(\VM\RecordingSessionBundle\Entity\RecordingSessionKeywordList $recordingSessionKeywordList)
    {
        foreach ($recordingSessionKeywordList as $keywordList) {
            $keywordList->addRecordingSession($this);
        }

        $this->RecordingSessionKeywordList = $recordingSessionKeywordList;
    }

    /**
     * Remove RecordingSessionKeywordList
     *
     * @param \VM\RecordingSessionBundle\Entity\RecordingSessionKeywordList $recordingSessionKeywordList
     */
    public function removeRecordingSessionKeywordList(\VM\RecordingSessionBundle\Entity\RecordingSessionKeywordList $recordingSessionKeywordList)
    {
        $this->RecordingSessionKeywordList->removeElement($recordingSessionKeywordList);
    }

    /**
     * Get RecordingSessionKeywordList
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecordingSessionKeywordList()
    {
        return $this->RecordingSessionKeywordList;
    }

    /**
     * Set User
     *
     * @param \VM\UserBundle\Entity\User $user
     * @return RecordingSession
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
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $RecordingSessionUser;


    /**
     * Add RecordingSessionUser
     *
     * @param \VM\RecordingSessionBundle\Entity\RecordingSessionUser $recordingSessionUser
     * @return RecordingSession
     */
    public function addRecordingSessionUser(\VM\RecordingSessionBundle\Entity\RecordingSessionUser $recordingSessionUser)
    {
        $this->RecordingSessionUser[] = $recordingSessionUser;
    
        return $this;
    }

    /**
     * Remove RecordingSessionUser
     *
     * @param \VM\RecordingSessionBundle\Entity\RecordingSessionUser $recordingSessionUser
     */
    public function removeRecordingSessionUser(\VM\RecordingSessionBundle\Entity\RecordingSessionUser $recordingSessionUser)
    {
        $this->RecordingSessionUser->removeElement($recordingSessionUser);
    }

    /**
     * Get RecordingSessionUser
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecordingSessionUser()
    {
        return $this->RecordingSessionUser;
    }
}