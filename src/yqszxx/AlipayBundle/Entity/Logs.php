<?php

namespace yqszxx\AlipayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logs
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Logs
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="trade_no", type="string", length=255)
     */
    private $tradeNo;

    /**
     * @var string
     *
     * @ORM\Column(name="trade_status", type="string", length=255)
     */
    private $tradeStatus;


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
     * Set tradeNo
     *
     * @param string $tradeNo
     * @return Logs
     */
    public function setTradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;

        return $this;
    }

    /**
     * Get tradeNo
     *
     * @return string 
     */
    public function getTradeNo()
    {
        return $this->tradeNo;
    }

    /**
     * Set tradeStatus
     *
     * @param string $tradeStatus
     * @return Logs
     */
    public function setTradeStatus($tradeStatus)
    {
        $this->tradeStatus = $tradeStatus;

        return $this;
    }

    /**
     * Get tradeStatus
     *
     * @return string 
     */
    public function getTradeStatus()
    {
        return $this->tradeStatus;
    }
}
