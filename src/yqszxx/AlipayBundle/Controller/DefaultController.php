<?php

namespace yqszxx\AlipayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use yqszxx\AlipayBundle\Alipay\TradeCreateByBuyer;

class DefaultController extends Controller
{
    /**
     * @Route("/alipay/debug")
     * @Template()
     */
    public function indexAction()
    {
        $name = new TradeCreateByBuyer($this->container);
        dump($name);
        return array('name' => 'yqszxx');
    }
}
