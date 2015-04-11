<?php

namespace yqszxx\AlipayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use yqszxx\AlipayBundle\Entity\yqlogs;

class DefaultController extends Controller
{
    /**
     * @Route("/alipay/debug", name="alipay_debug")
     */
    public function debugAction()
    {
        $tcb = $this->container->get('alipay_tcb');
        $requestBuilder = $tcb->getRequestBuilder();
        $parameters = $requestBuilder->getParametersBuilder();
        $parameters
            ->setOutTradeNo('1403021999052'.rand(100,999))
            ->setPrice('0.01')
            ->setSubject('yqgoods')
            ->setNotifyUrl($this->generateUrl('alipay_notify',array(),true))
            ->setReturnUrl($this->generateUrl('alipay_return',array(),true));
        $url = $requestBuilder->buildUrl($parameters);
        return $this->render('yqszxxAlipayBundle:Default:index.html.twig',array('content' => $url));
    }

    /**
     * @Route("/alipay/notify", name="alipay_notify")
     * @param Request $request
     * @return Response
     */
    public function notifyAction(Request $request)
    {
        $logs = new yqlogs();
        $tcb = $this->get('alipay_tcb');
        $result = $tcb->getNotifyHandler()->handle($request);
        $code = $result->getCode();
        $content = $result->getParameters();
        $logs->setArray(array('code' => $code,'request'=>$request,'result'=>$content));
        $em = $this->getDoctrine()->getManager();
        $em->persist($logs);
        $em->flush();

        return $result->getResponse();

    }

    /**
     * @Route("/alipay/return", name="alipay_return")
     * @return Response
     */
    public function notifyGetAction(Request $request)
    {
//        dump($request->query->all());
        return $this->render('yqszxxAlipayBundle:Default:index.html.twig',array('content'=>'ok'));
    }
}
