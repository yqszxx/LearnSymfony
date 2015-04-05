<?php

namespace yqszxx\AlipayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/alipay/debug", name="alipay_debug")
     * @Template()
     */
    public function indexAction()
    {
        $tcb = $this->container->get('alipay_tcb');
        $url = $tcb
            ->setSubject('TestGood')
            ->setPrice(0.01)
            ->setNotifyUrl('http://www.itfls.com/a.php')
            ->setReturnUrl('http://www.itfls.com/a.php')
            ->setOutTradeNo('8544601198343944')
            ->getUrl();
        return array('url' => $url);
    }
}
