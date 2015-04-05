<?php

namespace yqszxx\LearnSymfony\BootstrapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/yqszxx/zurb")
     * @Template()
     */
    public function indexAction()
    {
        return $this->render('yqszxxLearnSymfonyBootstrapBundle:Default:index.html.twig');
    }
}
