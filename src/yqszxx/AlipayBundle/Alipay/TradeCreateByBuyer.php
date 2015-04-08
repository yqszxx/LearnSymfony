<?php
/**
 * This file (TradeCreateByBuyer.php) is belong to project "yqszxxAlipayBundle".
 * Author: yqszxx (hby@itfls.com)
 */

namespace yqszxx\AlipayBundle\Alipay;

use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use yqszxx\AlipayBundle\Exception\NotYetImplementedException;
use yqszxx\AlipayBundle\Exception\RequiredParameterNotSetException;


/**
 * 支付宝标准双接口实现
 * Class TradeCreateByBuyer
 * @package yqszxx\AlipayBundle\Alipay
 */
class TradeCreateByBuyer
{

    /**
     *支付宝网关地址（新）
     */
    const ALIPAY_GATEWAY = 'https://mapi.alipay.com/gateway.do?';

    /**
     * @var ContainerInterface container
     */
    private $container;

    /**
     * @var array 请求参数数组
     */
    private $requestParameters;

    /**
     * @var array 异步通知参数数组
     */
    private $notifyParameters;

    /**
     * @var array 同步通知参数数组
     */
    private $returnParameters;

    /**
     * @var int 工作模式（1：请求、2：异步通知、3：同步通知）
     */
    private $mode;

    /**
     * @var string 错误信息
     */
    private $error;

    /**
     * @var string 合作身份者 ID，从 Parameter 中获取
     */
    private $partner;

    /**
     * @var string 卖家支付宝账号，从 Parameter 中获取
     */
    private $sellerEmail;

    /**
     * @var string 签名私钥
     */
    private $key;

    /**
     * @var string 签名方式（DSA、RSA、MD5 三个值可选,必须大写）
     */
    private $signType;

    /**
     * @var Response 最终返回给支付宝的响应
     */
    private $notifyResponse;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->partner = strtolower(trim($this->container->getParameter('alipay_partner')));
        $this->sellerEmail = strtolower(trim($this->container->getParameter('alipay_seller_email')));
        $this->key = strtolower(trim($this->container->getParameter('alipay_key')));

        $this->requestParameters = array(
            //基本参数
            'service'               =>          'trade_create_by_buyer', //接口名称
            'partner'               =>          $this->partner, //合作身份者 ID
            '_input_charset'        =>          'UTF-8', //参数编码字符集
            'notify_url'            =>          $this->container->get('router')->generate('alipay_notify',array(),true), //服务器异步通知页面路径
            'return_url'            =>          $this->container->get('router')->generate('alipay_return', array(), true), //页面跳转同步通知页面路径
            //业务参数
            'out_trade_no'          =>          null, //商户网站唯一订单号
            'subject'               =>          null, //商品名称
            'payment_type'          =>          '1', //收款类型（只支持 1:商品购买）
            'logistics_type'        =>          'POST', //物流类型（平邮）
            'logistics_fee'         =>          0.00, //物流费用
            'logistics_payment'     =>          'SELLER_PAY', //物流支付类型（卖家支付）
            'seller_email'          =>          $this->sellerEmail, //卖家支付宝账号，从Parameter中获取
            'price'                 =>          null, //商品单价
            'quantity'              =>          1, //商品数量，因为现在是充值接口所以设为1
        );

        return $this;
    }

    /**
     * 在请求专用方法开始处调用
     * @param $methodName string 需要传入魔术常量__FUNCTION__
     */
    protected function requireModeIsRequest($methodName){
        if (empty($this->mode)) {
            $this->mode = 1;
        }
        if($this->mode != 1){
            throw new \BadMethodCallException('You can only call the '.$methodName.'() method when generating a request!');
        }
    }

    /**
     * 在禁止生成请求时使用的方法开始处调用
     * @param $methodName string 需要传入魔术常量__FUNCTION__
     */
    protected function requireModeIsNotRequest($methodName){
        if($this->mode == 1){
            throw new \BadMethodCallException('You can only call the '.$methodName.'() method when handling a notify or a return!');
        }
    }

    /**
     * 在异步通知专用方法开始处调用
     * @param $methodName string 需要传入魔术常量__FUNCTION__
     */
    protected function requireModeIsNotify($methodName){
        if (empty($this->mode)) {
            $this->mode = 2;
        }
        if($this->mode != 2){
            throw new \BadMethodCallException('You can only call the '.$methodName.'() method when handling a notify!');
        }
    }

    /**
     * 在同步通知专用方法开始处调用
     * @param $methodName string 需要传入魔术常量__FUNCTION__
     */
    protected function requireModeIsReturn($methodName){
        if (empty($this->mode)) {
            $this->mode = 3;
        }
        if($this->mode != 3){
            throw new \BadMethodCallException('You can only call the '.$methodName.'() method when handling a return!');
        }
    }

    /**
     * 判断是否工作在请求模式下
     * @return bool
     */
    protected function modeIsRequest(){
        if($this->mode == 1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 判断是否工作在异步通知模式下
     * @return bool
     */
    protected function modeIsNotify(){
        if($this->mode == 2){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 判断是否工作在同步通知模式下
     * @return bool
     */
    protected function modeIsReturn(){
        if($this->mode == 1){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取处理同、异步通知时的错误
     * @return string
     */
    public function getError()
    {
        $this->requireModeIsNotRequest(__FUNCTION__);
        return $this->error;
    }

    /**
     * 获取异步通知时返回给支付宝的响应
     * @return Response
     */
    public function getNotifyResponse()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        return $this->notifyResponse;
    }

    /**
     * 获取异步通知时通知的发送时间
     * @return string 格式为 yyyy-MM-dd HH:mm:ss
     */
    public function getNotifyTime()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        return $this->notifyParameters['notify_time'];
    }

    /**
     * 获取异步通知时创建订单生成的交易号
     * @return string 支付宝交易号
     */
    public function getNotifyTradeNo()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        return $this->notifyParameters['trade_no'];
    }

    /**
     * 获取异步通知时买家支付宝账号
     * @return string
     */
    public function getNotifyBuyerEmail()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        return $this->notifyParameters['buyer_email'];
    }

    /**
     * 获取异步通知时交易状态
     * @return string
     */
    public function getNotifyTradeStatus()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        return $this->notifyParameters['trade_status'];
    }

    /**
     * 获取异步通知时商户提供的唯一订单号
     * @return string
     */
    public function getNotifyOutTradeNo()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        return $this->notifyParameters['out_trade_no'];
    }

    /**
     * 获取异步通知时的交易总额
     * @return string
     */
    public function getNotifyTotalFee()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        return $this->notifyParameters['total_fee'];
    }

    /**
     * 获取异步通知时的交易创建时间
     * @return string
     */
    public function getNotifyGMTCreate()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        return $this->notifyParameters['gmt_create'];
    }

    /**
     * 获取异步通知时的交易创建时间
     * @return string
     */
    public function getNotifyGMTPayment()
    {
        $this->requireModeIsNotify(__FUNCTION__);
        throw new NotYetImplementedException('Getting payment succeed time is not implemented yet.');
//        return $this->notifyParameters['gmt_payment'];
    }

    /**
     * 设置请求时的商品名称
     * @param string $subject 要设置为的商品名称
     * @return $this
     */
    public function setRequestSubject($subject)
    {
        $this->requireModeIsRequest(__FUNCTION__);
        $this->requestParameters['subject'] = $subject;
        return $this;
    }

    /**
     * 设置请求时的商户订单号
     * @param string $outTradeNo 要设置为的商户订单号
     * @return $this
     */
    public function setRequestOutTradeNo($outTradeNo)
    {
        $this->requireModeIsRequest(__FUNCTION__);
        $this->requestParameters['out_trade_no'] = $outTradeNo;
        return $this;
    }

    /**
     * 设置请求时的单价
     * @param float $price 要设置为的单价
     * @return $this
     */
    public function setRequestPrice($price)
    {
        $this->requireModeIsRequest(__FUNCTION__);
        $this->requestParameters['price'] = $price;
        return $this;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param bool $isUrl 是否进行Url编码
     * @return string 拼接完成以后的字符串
     */
    protected function getLinkString($isUrl = false) {

        global $parameters;
        if($this->modeIsRequest()){
            $parameters = &$this->requestParameters;
        }elseif($this->modeIsNotify()){
            $parameters = &$this->notifyParameters;
        }elseif($this->modeIsReturn()){
            $parameters = &$this->returnParameters;
        }

        $linkString = '';
        foreach ($parameters as $key => $value) {
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
    protected function processParameters() {

        global $parameters;
        if($this->modeIsRequest()){
            $parameters = &$this->requestParameters;
        }elseif($this->modeIsNotify()){
            $parameters = &$this->notifyParameters;
        }elseif($this->modeIsReturn()){
            $parameters = &$this->returnParameters;
        }

        ksort($parameters);

        foreach($parameters as $key => $value){
            if($key == "sign" || $key == "sign_type" || $value === null){
                unset($parameters[$key]);
            }
        }

        $neededParameters = null;
        if($this->modeIsRequest()){
            $neededParameters = array(
                'return_url',
                'out_trade_no',
                'subject',
                'price',
            );
        }elseif($this->modeIsNotify()){
            $neededParameters = array(
                'notify_id',
                'notify_time',
                'trade_no',
                'buyer_email',
                'trade_status',
                'out_trade_no',
                'total_fee',
            );
        }

        foreach($neededParameters as $value){
            if(!array_key_exists($value, $parameters)){
                throw new RequiredParameterNotSetException($value);
            }
        }

        reset($parameters);

        return $this;
    }

    /**
     * 选取指定的签名方法对参数签名，并将签名和签名方法写入参数数组
     * @return $this|string 在1模式下返回$this，在2、3模式下返回加密结果字符串
     */
    protected function signParameters() {

        global $sign;
        switch ($this->signType) {
            case "MD5" :
                if($this->modeIsRequest()){
                    $this->requestParameters['sign'] = md5($this->getLinkString().$this->key);
                    $this->requestParameters['sign_type'] = $this->signType;
                }else{
                    $sign = md5($this->getLinkString().$this->key);
                }
                break;
            default :
                throw new NotYetImplementedException('Other sign types are not implemented yet.');
        }

        return ($this->modeIsRequest() ? $this : $sign);
    }

    /**
     * 获取请求url
     * @return string 生成的请求url
     */
    public function getRequestUrl() {
        $this->requireModeIsRequest(__FUNCTION__);
        $this->signType = strtoupper(trim($this->container->getParameter('alipay_sign_type')));
        $this->processParameters()->signParameters();
        return $this::ALIPAY_GATEWAY.$this->getLinkString(true);
    }

    /**
     * 处理异步通知
     * @param Request $request 需要传入Request对象
     * @return $this
     */
    public function handleNotify($request){
        $this->requireModeIsNotify(__FUNCTION__);
        $this->notifyResponse = new Response();

        if($request->getMethod()!='POST'){
            throw new MethodNotAllowedHttpException(array('POST'));
        }

        $this->notifyParameters = $request->request->all();

        $originalNotifySign = $this->notifyParameters['sign'];
        $this->signType = $this->notifyParameters['sign_type'];
        if($this->processParameters()->signParameters() != $originalNotifySign){ //数据加密校验不符
            $this->notifyResponse->setContent('failure');
            $this->error = 'encryptVerificationFailed';
            return $this;
        }

        $httpClient = new Client();
        $verifyResult =
            $httpClient
                ->get(
                    $this::ALIPAY_GATEWAY,
                    array(
                        'service'           =>  'notify_verify',
                        'partner'           =>  $this->partner,
                        'notify_id'         =>  $this->notifyParameters['notify_id'],
                        )
                )
                ->getBody()
                ->getContents();
        if($verifyResult != 'true'){ //验证请求真实性失败
            $this->notifyResponse->setContent('failure');
            $this->error = 'authenticityVerificationFailed';
            return $this;
        }

        $this->notifyResponse->setContent('success');
        return $this;
    }
}