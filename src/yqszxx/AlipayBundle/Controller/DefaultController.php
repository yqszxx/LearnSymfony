<?php

namespace yqszxx\AlipayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/alipay/debug", name="alipay_debug")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $tcb = $this->container->get('alipay_tcb');
        $url = $tcb
            ->setSubject('ShiHongsuo')
            ->setPrice($request->get('price'))
            ->setReturnUrl('http://www.itfls.com/a.php')
            ->setOutTradeNo('85446011983439'.rand(0,99))
            ->getUrl();
        return $this->render('yqszxxAlipayBundle:Default:index.html.twig',array('url' => $url));
    }
//
//    /**
//     * @Route("/alipay/notify", name="alipay_notify")
//     * @Method("POST")
//     * @param Request $request
//     */
//    public function notifyPostAction(Request $request)
//    {
//
//    }
//
//    /**
//     * @Route("/alipay/tcb/notify", name="alipay_notify")
//     * @Method("GET")
//     * @param Request $request
//     */
//    public function notifyGetAction(Request $request)
//    {
//        $tcb = $this->container->get('alipay_tcb');
//        $tcb->setCallbackMethod('')
//            ->handleNotify($request);
//    }
}
