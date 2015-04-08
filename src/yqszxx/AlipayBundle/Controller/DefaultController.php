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
        $url = $tcb
            ->setRequestSubject('yqTestGood')
            ->setRequestPrice('0.01')
            ->setRequestOutTradeNo('1403021999052'.rand(100,999))
            ->getRequestUrl();
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
        $logs->setArray($request->request->all());
//        $tcb = $this->get('alipay_tcb');
//        $tcb->handleNotify($request)->getError();
        $em = $this->getDoctrine()->getManager();
        $em->persist($logs);
        $em->flush();

        return new Response('success');

    }

    /**
     * @Route("/alipay/return", name="alipay_return")
     * @return Response
     */
    public function notifyGetAction()
    {
        return $this->render('yqszxxAlipayBundle:Default:index.html.twig');
    }
}
