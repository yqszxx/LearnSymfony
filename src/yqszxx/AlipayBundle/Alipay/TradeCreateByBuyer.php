<?php
/**
 * This file (TradeCreateByBuyer.php) is belong to project "yqszxxAlipayBundle".
 * Author: yqszxx (hby@itfls.com)
 */

namespace yqszxx\AlipayBundle\Alipay;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 支付宝标准双接口实现
 * Class TradeCreateByBuyer
 * @package yqszxx\AlipayBundle\Alipay
 */
class TradeCreateByBuyer
{
    private $container;

    private $config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->config['partner'] = $this->container->getParameter('alipay.partner');
        $this->config['sellerEmail'] = $this->container->getParameter('alipay.seller_email');
        $this->config['key'] = $this->container->getParameter('alipay.key');
        $this->config['signType'] = $this->container->getParameter('alipay.sign_type');
    }

    public function getRequestBuilder(){
        return new TradeCreateByBuyerRequestBuilder($this->config);
    }

    public function getNotifyHandler(){
        return new TradeCreateByBuyerNotifyHandler($this->config);
    }
}