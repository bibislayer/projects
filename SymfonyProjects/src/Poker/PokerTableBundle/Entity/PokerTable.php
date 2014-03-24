<?php

namespace Poker\PokerTableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PokerTable
 */
class PokerTable
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
     * @var integer
     */
    private $round;

    /**
     * @var integer
     */
    private $big_blind;

    /**
     * @var integer
     */
    private $small_blind;

    /**
     * @var integer
     */
    private $elapse_time;

    /**
     * @var array
     */
    private $cards;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $PokerUser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->PokerUser = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return PokerTable
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
     * Set round
     *
     * @param integer $round
     * @return PokerTable
     */
    public function setRound($round)
    {
        $this->round = $round;
    
        return $this;
    }

    /**
     * Get round
     *
     * @return integer 
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set big_blind
     *
     * @param integer $bigBlind
     * @return PokerTable
     */
    public function setBigBlind($bigBlind)
    {
        $this->big_blind = $bigBlind;
    
        return $this;
    }

    /**
     * Get big_blind
     *
     * @return integer 
     */
    public function getBigBlind()
    {
        return $this->big_blind;
    }

    /**
     * Set small_blind
     *
     * @param integer $smallBlind
     * @return PokerTable
     */
    public function setSmallBlind($smallBlind)
    {
        $this->small_blind = $smallBlind;
    
        return $this;
    }

    /**
     * Get small_blind
     *
     * @return integer 
     */
    public function getSmallBlind()
    {
        return $this->small_blind;
    }

    /**
     * Set elapse_time
     *
     * @param integer $elapseTime
     * @return PokerTable
     */
    public function setElapseTime($elapseTime)
    {
        $this->elapse_time = $elapseTime;
    
        return $this;
    }

    /**
     * Get elapse_time
     *
     * @return integer 
     */
    public function getElapseTime()
    {
        return $this->elapse_time;
    }

    /**
     * Set cards
     *
     * @param array $cards
     * @return PokerTable
     */
    public function setCards($cards)
    {
        $this->cards = $cards;
    
        return $this;
    }

    /**
     * Get cards
     *
     * @return array 
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * Add PokerUser
     *
     * @param \Poker\PokerTableBundle\Entity\PokerUser $pokerUser
     * @return PokerTable
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
    /**
     * @var array
     */
    private $cardsUsed;


    /**
     * Set cardsUsed
     *
     * @param array $cardsUsed
     * @return PokerTable
     */
    public function setCardsUsed($cardsUsed)
    {
        $this->cardsUsed = $cardsUsed;
    
        return $this;
    }

    /**
     * Get cardsUsed
     *
     * @return array 
     */
    public function getCardsUsed()
    {
        return $this->cardsUsed;
    }
}