<?php

namespace VM\QuestionnaireBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionnaireElementPayment
 */
class QuestionnaireElementPayment
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean
     */
    private $payment;

    /**
     * @var string
     */
    private $text_payment;

    /**
     * @var float
     */
    private $payment_amount_before;

    /**
     * @var float
     */
    private $payment_amount_after;

    /**
     * @var float
     */
    private $payment_vat;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var \VM\QuestionnaireBundle\Entity\QuestionnaireElement
     */
    private $QuestionnaireElement;


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
     * Set payment
     *
     * @param boolean $payment
     * @return QuestionnaireElementPayment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    
        return $this;
    }

    /**
     * Get payment
     *
     * @return boolean 
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set text_payment
     *
     * @param string $textPayment
     * @return QuestionnaireElementPayment
     */
    public function setTextPayment($textPayment)
    {
        $this->text_payment = $textPayment;
    
        return $this;
    }

    /**
     * Get text_payment
     *
     * @return string 
     */
    public function getTextPayment()
    {
        return $this->text_payment;
    }

    /**
     * Set payment_amount_before
     *
     * @param float $paymentAmountBefore
     * @return QuestionnaireElementPayment
     */
    public function setPaymentAmountBefore($paymentAmountBefore)
    {
        $this->payment_amount_before = $paymentAmountBefore;
    
        return $this;
    }

    /**
     * Get payment_amount_before
     *
     * @return float 
     */
    public function getPaymentAmountBefore()
    {
        return $this->payment_amount_before;
    }

    /**
     * Set payment_amount_after
     *
     * @param float $paymentAmountAfter
     * @return QuestionnaireElementPayment
     */
    public function setPaymentAmountAfter($paymentAmountAfter)
    {
        $this->payment_amount_after = $paymentAmountAfter;
    
        return $this;
    }

    /**
     * Get payment_amount_after
     *
     * @return float 
     */
    public function getPaymentAmountAfter()
    {
        return $this->payment_amount_after;
    }

    /**
     * Set payment_vat
     *
     * @param float $paymentVat
     * @return QuestionnaireElementPayment
     */
    public function setPaymentVat($paymentVat)
    {
        $this->payment_vat = $paymentVat;
    
        return $this;
    }

    /**
     * Get payment_vat
     *
     * @return float 
     */
    public function getPaymentVat()
    {
        return $this->payment_vat;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return QuestionnaireElementPayment
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
     * @return QuestionnaireElementPayment
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
     * Set QuestionnaireElement
     *
     * @param \VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement
     * @return QuestionnaireElementPayment
     */
    public function setQuestionnaireElement(\VM\QuestionnaireBundle\Entity\QuestionnaireElement $questionnaireElement = null)
    {
        $this->QuestionnaireElement = $questionnaireElement;
    
        return $this;
    }

    /**
     * Get QuestionnaireElement
     *
     * @return \VM\QuestionnaireBundle\Entity\QuestionnaireElement 
     */
    public function getQuestionnaireElement()
    {
        return $this->QuestionnaireElement;
    }
}