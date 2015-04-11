<?php
/**
 * This file (TradeCreateByBuyerRequestBuilder.php) is belong to project "LearnSymfony".
 * Author: yqszxx
 * Created At: 15-4-10-下午9:50
 */

namespace yqszxx\AlipayBundle\Alipay;


class TradeCreateByBuyerRequestBuilder extends TradeCreateByBuyerRequestParameters
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function getParametersBuilder(){
        return new TradeCreateByBuyerRequestParameters($this->config);
    }

    /**
     * 获取请求url
     * @param TradeCreateByBuyerRequestParameters $parametersClass
     * @return string 生成的请求url
     */
    public function buildUrl(TradeCreateByBuyerRequestParameters $parametersClass){
        $parameters = $parametersClass->parameters;
        $signType = $this->config['signType'];
        $parameters = self::processParameters($parameters);
        $sign = self::getSignature($parameters,$this->config['key'],$signType);
        $parameters['sign'] = $sign;
        $parameters['sign_type'] = $signType;
        $url = self::ALIPAY_GATEWAY . $this->getLinkString($parameters,true);
        return $url;
    }
}