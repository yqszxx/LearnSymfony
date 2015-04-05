<?php
/**
 * This file (TradeCreateByBuyer.php) is belong to project "sandbox".
 * Author: yqszxx
 * Created At: 15-4-3-下午11:51
 */

namespace yqszxx\AlipayBundle\Alipay;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Exception\NotImplementedException;
use yqszxx\AlipayBundle\Exception\NotYetImplementedException;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use yqszxx\AlipayBundle\Exception\RequiredParameterNotSetException;


class TradeCreateByBuyer extends ContainerAware
{
    /**
     *支付宝网关地址（新）
     */
    const alipay_gateway = 'https://mapi.alipay.com/gateway.do?';

    private $parameters;

    private $key;

    private $signType;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->parameters = array(
            //基本参数
            'service'               =>          'trade_create_by_buyer', //接口名称
            'partner'               =>          strtolower(trim($this->container->getParameter('alipay_partner'))), //合作身份者ID，从Parameter中获取
            '_input_charset'        =>          'UTF-8', //参数编码字符集
            'notify_url'            =>          null, //服务器异步通知页面路径
            'return_url'            =>          null, //页面跳转同步通知页面路径
            //业务参数
            'out_trade_no'          =>          null, //商户网站唯一订单号
            'subject'               =>          null, //商品名称
            'payment_type'          =>          '1', //收款类型（只支持 1:商品购买）
            'logistics_type'        =>          'POST', //物流类型（快递）
            'logistics_fee'         =>          0.00, //物流费用
            'logistics_payment'     =>          'SELLER_PAY', //物流支付类型（卖家支付）
            'seller_email'          =>          strtolower(trim($this->container->getParameter('alipay_seller_email'))), //卖家支付宝账号，从Parameter中获取
            'price'                 =>          null, //商品单价
            'quantity'              =>          1, //商品数量，因为现在是充值接口所以设为1
        );

        $this->signType = strtoupper(trim($this->container->getParameter('alipay_sign_type')));
        $this->key = strtolower(trim($this->container->getParameter('alipay_key')));

        return $this;
    }

    /**
     * 设置商品名称
     * @param string $subject 要设置为的商品名称
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->parameters['subject'] = $subject;
        return $this;
    }

    /**
     * 设置异步通知页面路径
     * @param string $notifyUrl 要设置为的异步通知页面路径
     * @return $this
     */
    public function setNotifyUrl($notifyUrl)
    {
        $this->parameters['notify_url'] = $notifyUrl;
        return $this;
    }

    /**
     * 设置同步通知页面路径
     * @param string $returnUrl 要设置为的同步通知页面路径
     * @return $this
     */
    public function setReturnUrl($returnUrl)
    {
        $this->parameters['return_url'] = $returnUrl;
        return $this;
    }

    /**
     * 设置订单号
     * @param string $outTradeNo 要设置为的订单号
     * @return $this
     */
    public function setOutTradeNo($outTradeNo)
    {
        $this->parameters['out_trade_no'] = $outTradeNo;
        return $this;
    }

    /**
     * 设置单价
     * @param float $price 要设置为的单价
     * @return $this
     */
    public function setPrice($price)
    {
        $this->parameters['price'] = $price;
        return $this;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param bool $isUrl 是否进行Url编码
     * @return string 拼接完成以后的字符串
     */
    protected function getLinkString($isUrl = false) {
        $linkString = '';
        foreach ($this->parameters as $key => $value) {
            $linkString.=$key.'='.($isUrl ? urlencode($value) : $value).'&';
        }
        //去掉最后一个&字符
        $linkString = substr($linkString,0,count($linkString)-2);

        return $linkString;
    }

    /**
     * 对数组排序、去空值、检测必需的参数
     * @return $this
     */
    private function processParameters() {

        ksort($this->parameters);

        foreach($this->parameters as $key => $value){
            if($key == "sign" || $key == "sign_type" || $value === null){
                unset($this->parameters[$key]);
            }
        }

        $neededParameters = array(
            'notify_url',
            'return_url',
            'out_trade_no',
            'subject',
            'price',
        );
        foreach($neededParameters as $value){
            if(!array_key_exists($value, $this->parameters)){
                throw new RequiredParameterNotSetException($value);
            }
        }

        reset($this->parameters);

        return $this;
    }

    /**
     * 选取指定的签名方法对参数签名，并将签名和签名方法写入参数数组
     * @return $this
     */
    protected function signParameters() {
        switch ($this->signType) {
            case "MD5" :
                $this->parameters['sign'] = md5($this->getLinkString().$this->key);
                $this->parameters['sign_type'] = $this->signType;
                break;
            default :
                throw new NotYetImplementedException('Other sign types are not implemented yet.');
        }

        return $this;
    }

    /**
     * 获取请求url
     * @return string 生成的url
     */
    public function getUrl() {
        $this->processParameters()->signParameters();
        return $this::alipay_gateway.$this->getLinkString(true);
    }
}