<?php
/**
 * This file (TradeCreateByBuyerNotifyResult.php) is belong to project "LearnSymfony".
 * Author: yqszxx
 * Created At: 15-4-11-下午9:17
 */

namespace yqszxx\AlipayBundle\Alipay;


use Symfony\Component\HttpFoundation\Response;

class TradeCreateByBuyerNotifyResult {
    protected $code;
    protected $response;
    protected $parameters;

    function __construct(array $parameters, $success, $code)
    {
        $this->parameters = $parameters;
        $this->code = $code;
        $this->response = new Response(($success ? 'success' : '<html>failure</html>'));
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

}