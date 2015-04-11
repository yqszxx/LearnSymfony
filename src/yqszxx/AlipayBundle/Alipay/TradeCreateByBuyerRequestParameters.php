<?php
/**
 * This file (TradeCreateByBuyerRequestParameters.php) is belong to project "LearnSymfony".
 * Author: yqszxx
 * Created At: 15-4-11-上午11:12
 */

namespace yqszxx\AlipayBundle\Alipay;

class TradeCreateByBuyerRequestParameters extends TradeCreateByBuyerBase
{
    protected $parameters;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->parameters = array(
            //基本参数
            'service'               =>          'trade_create_by_buyer', //接口名称
            'partner'               =>          $this->config['partner'], //合作身份者 ID
            '_input_charset'        =>          'UTF-8', //参数编码字符集
            'notify_url'            =>          null, //服务器异步通知页面路径
            'return_url'            =>          null, //页面跳转同步通知页面路径
            //业务参数
            'out_trade_no'          =>          null, //商户网站唯一订单号
            'subject'               =>          null, //商品名称
            'payment_type'          =>          '1', //收款类型（只支持 1:商品购买）
            'logistics_type'        =>          'POST', //物流类型（平邮）
            'logistics_fee'         =>          0.00, //物流费用
            'logistics_payment'     =>          'SELLER_PAY', //物流支付类型（卖家支付）
            'seller_email'          =>          $this->config['sellerEmail'], //卖家支付宝账号
            'price'                 =>          null, //商品单价
            'quantity'              =>          1, //商品数量，因为现在是充值接口所以设为1
        );
    }

    /**
     * 设置请求时的商品名称
     * @param string $subject 要设置为的商品名称
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->parameters['subject'] = $subject;
        return $this;
    }

    /**
     * 设置请求时的商户订单号
     * @param string $outTradeNo 要设置为的商户订单号
     * @return $this
     */
    public function setOutTradeNo($outTradeNo)
    {
        $this->parameters['out_trade_no'] = $outTradeNo;
        return $this;
    }

    /**
     * 设置请求时的单价
     * @param float $price 要设置为的单价
     * @return $this
     */
    public function setPrice($price)
    {
        $this->parameters['price'] = $price;
        return $this;
    }

    public function setNotifyUrl($notifyUrl){
        $this->parameters['notify_url'] = $notifyUrl;
        return $this;
    }

    public function setReturnUrl($returnUrl){
        $this->parameters['return_url'] = $returnUrl;
        return $this;
    }
}