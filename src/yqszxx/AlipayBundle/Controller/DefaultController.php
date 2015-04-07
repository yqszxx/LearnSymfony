<?php

namespace yqszxx\AlipayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use yqszxx\AlipayBundle\Entity\Logs;

class DefaultController extends Controller
{
    /**
     * @Route("/alipay/debug", name="alipay_debug")
     */
    public function debugAction()
    {
        $tcb = $this->container->get('alipay_tcb');
        $url = $tcb
            ->setRequestSubject('yqTestGood')
            ->setRequestPrice('0.01')
            ->setRequestReturnUrl($this->generateUrl('alipay_return', array(), true))
            ->setRequestOutTradeNo('1403021999052612')
            ->getRequestUrl();
        return $this->render('yqszxxAlipayBundle:Default:index.html.twig',array('content' => $url));
    }

    /**
     * @Route("/alipay/notify", name="alipay_notify")
     * @param Request $request
     */
    public function notifyAction(Request $request)
    {
        $logs = new Logs();
        $tcb = $this->get('alipay_tcb');
        $tradeNo = 'AliTN='.$tcb->handleNotify($request)->getNotifyTradeNo().' YqTN='.$tcb->getNotifyOutTradeNo().' err='.$tcb->getError();
        $logs->setTradeNo($tradeNo)->setObject($tcb);
        $em = $this->getDoctrine()->getManager();
        $em->persist($logs);
        $em->flush();

    }

    /**
     * @Route("/alipay/return", name="alipay_return")
     * @param Request $request
     */
    public function notifyGetAction(Request $request)
    {
        return $this->render('yqszxxAlipayBundle:Default:index.html.twig');
    }
}
