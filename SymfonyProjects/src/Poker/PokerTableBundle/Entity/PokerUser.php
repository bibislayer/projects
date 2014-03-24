<?php

namespace Poker\PokerTableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PokerUser
 */
class PokerUser
{
    /**
     * @var integer
     */
    private $place;

    /**
     * @var integer
     */
    private $money;

    /**
     * @var integer
     */
    private $money_used;

    /**
     * @var \Poker\PokerTableBundle\Entity\PokerTable
     */
    private $PokerTable;

    /**
     * @var \Poker\PokerTableBundle\Entity\PokerTable
     */
    private $User;


    /**
     * Set place
     *
     * @param integer $place
     * @return PokerUser
     */
    public function setPlace($place)
    {
        $this->place = $place;
    
        return $this;
    }

    /**
     * Get place
     *
     * @return integer 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set money
     *
     * @param integer $money
     * @return PokerUser
     */
    public function setMoney($money)
    {
        $this->money = $money;
    
        return $this;
    }

    /**
     * Get money
     *
     * @return integer 
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Set money-used
     *
     * @param integer $moneyUsed
     * @return PokerUser
     */
    public function setMoneyUsed($moneyUsed)
    {
        $this->money_used = $moneyUsed;
    
        return $this;
    }

    /**
     * Get money-used
     *
     * @return integer 
     */
    public function getMoneyUsed()
    {
        return $this->money_used;
    }

    /**
     * Set PokerTable
     *
     * @param \Poker\PokerTableBundle\Entity\PokerTable $pokerTable
     * @return PokerUser
     */
    public function setPokerTable(\Poker\PokerTableBundle\Entity\PokerTable $pokerTable = null)
    {
        $this->PokerTable = $pokerTable;
    
        return $this;
    }

    /**
     * Get PokerTable
     *
     * @return \Poker\PokerTableBundle\Entity\PokerTable 
     */
    public function getPokerTable()
    {
        return $this->PokerTable;
    }

    /**
     * Set User
     *
     * @param \Poker\UserBundle\Entity\User $user
     * @return PokerUser
     */
    public function setUser(\Poker\UserBundle\Entity\User $user = null)
    {
        $this->User = $user;
    
        return $this;
    }

    /**
     * Get User
     *
     * @return \Poker\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->User;
    }
    /**
     * @var integer
     */
    private $id;


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
     * @var array
     */
    private $cards;


    /**
     * Set cards
     *
     * @param array $cards
     * @return PokerUser
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
     * @var boolean
     */
    private $big_blind;

    /**
     * @var boolean
     */
    private $small_blind;


    /**
     * Set big_blind
     *
     * @param boolean $bigBlind
     * @return PokerUser
     */
    public function setBigBlind($bigBlind)
    {
        $this->big_blind = $bigBlind;
    
        return $this;
    }

    /**
     * Get big_blind
     *
     * @return boolean 
     */
    public function getBigBlind()
    {
        return $this->big_blind;
    }

    /**
     * Set small_blind
     *
     * @param boolean $smallBlind
     * @return PokerUser
     */
    public function setSmallBlind($smallBlind)
    {
        $this->small_blind = $smallBlind;
    
        return $this;
    }

    /**
     * Get small_blind
     *
     * @return boolean 
     */
    public function getSmallBlind()
    {
        return $this->small_blind;
    }
    /**
     * @var integer
     */
    private $status;


    /**
     * Set status
     *
     * @param integer $status
     * @return PokerUser
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}