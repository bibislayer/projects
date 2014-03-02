<?php

namespace VM\RecordingSessionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecordingSessionKeywordList
 */
class RecordingSessionKeywordList
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
    private $slug;

    /**
     * @var \VM\RecordingSessionBundle\Entity\RecordingSession
     */
    private $RecordingSession;


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
     * @return RecordingSessionKeywordList
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
     * Set slug
     *
     * @param string $slug
     * @return RecordingSessionKeywordList
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
    
    public function addRecordingSession(\VM\RecordingSessionBundle\Entity\RecordingSession $recordingSession)
    {
        if (!$this->RecordingSession->contains($recordingSession)) {
            $this->RecordingSession->add($recordingSession);
        }
    }

    /**
     * Set RecordingSession
     *
     * @param \VM\RecordingSessionBundle\Entity\RecordingSession $recordingSession
     * @return RecordingSessionKeywordList
     */
    public function setRecordingSession(\VM\RecordingSessionBundle\Entity\RecordingSession $recordingSession = null)
    {
        $this->RecordingSession = $recordingSession;
    
        return $this;
    }

    /**
     * Get RecordingSession
     *
     * @return \VM\RecordingSessionBundle\Entity\RecordingSession 
     */
    public function getRecordingSession()
    {
        return $this->RecordingSession;
    }
}