<?php
/**
 * This file (TradeCreateByBuyerNotify.php) is belong to project "LearnSymfony".
 * Author: yqszxx
 * Created At: 15-4-9-下午9:22
 */

namespace yqszxx\AlipayBundle\Alipay;


use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
class TradeCreateByBuyerNotifyHandler extends TradeCreateByBuyerBase
{
    /**
     * 处理异步通知
     * @param Request $request 需要传入Request对象
     */
    public function handle(Request $request){

        if($request->getMethod()!='POST'){
            throw new MethodNotAllowedHttpException(array('POST'));
        }

        $parameters = $request->request->all();

        $originalSign = $parameters['sign'];
        $signType = $parameters['sign_type'];
        $parameters = self::processParameters($parameters);

        if(self::getSignature(
                self::getLinkString($parameters),$this->config['key'],$signType
            ) != $originalSign){ //数据加密校验不符
            return new TradeCreateByBuyerNotifyResult(null,false,1);
        }

        $httpClient = new Client();
        if($httpClient->get(
                $this::ALIPAY_GATEWAY,
                array(
                    'service'           =>  'notify_verify',
                    'partner'           =>  $this->config['partner'],
                    'notify_id'         =>  $parameters['notify_id'],
                    )
            )
                ->getBody()
                ->getContents() != 'true'){ //验证请求真实性失败
            return new TradeCreateByBuyerNotifyResult(null,false,2);
        }

        return new TradeCreateByBuyerNotifyResult($parameters,true,0);
    }
}