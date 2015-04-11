<?php
/**
 * This file (TradeCreateByBuyerBase.php) is belong to project "LearnSymfony".
 * Author: yqszxx
 * Created At: 15-4-9-下午10:50
 */

namespace yqszxx\AlipayBundle\Alipay;

use yqszxx\AlipayBundle\Exception\NotYetImplementedException;

class TradeCreateByBuyerBase
{
    /**
     *支付宝网关地址（新）
     */
    const ALIPAY_GATEWAY = 'https://mapi.alipay.com/gateway.do?';

    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param bool $isUrl 是否进行Url编码
     * @return string 拼接完成以后的字符串
     */
    protected static function getLinkString($parameters, $isUrl = false) {

        $linkString = '';
        foreach ($parameters as $key => $value) {
            $linkString.=$key.'='.($isUrl ? urlencode($value) : $value).'&';
        }
        //去掉最后一个&字符
        $linkString = substr($linkString,0,count($linkString)-2);

        return $linkString;
    }

    /**
     * 对数组排序、去空值
     * @param array $parameters
     * @return array
     */
    protected static function processParameters($parameters) {

        ksort($parameters);

        foreach($parameters as $key => $value){
            if($key == "sign" || $key == "sign_type" || $value === null){
                unset($parameters[$key]);
            }
        }

        reset($parameters);

        return $parameters;
    }

    /**
     * 选取指定的签名方法对参数签名，并将签名和签名方法写入参数数组
     * @param $parameters
     * @param $key
     * @param $signType
     * @return string
     */
    protected static function getSignature($parameters, $key, $signType) {

        $linkString = self::getLinkString($parameters);

        global $signature;
        switch ($signType) {
            case "MD5" :
                    $signature = md5($linkString.$key);
                break;
            default :
                throw new NotYetImplementedException('Other sign types are not implemented yet.');
        }

        return $signature;
    }


}