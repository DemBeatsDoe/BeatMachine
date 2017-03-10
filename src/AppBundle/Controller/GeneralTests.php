<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GeneralTests extends Controller
{
    /**
     * @Route("/seamless")
     */
    public function indexAction(Request $request)
    {
        return $this->render('tests/seamlessTest.html.twig', array());
    }
    /**
     * @Route("/seamless1")
     */
    public function page1Action(Request $request)
    {
        return $this->render('tests/seamlessPage1.html.twig', array());
    }
}