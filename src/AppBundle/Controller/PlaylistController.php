<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PlaylistController extends Controller
{
    /**
     * @Route("/playlist")
     */
    public function indexAction()
    {
        return $this->render('playlist.html.twig');
    }
}
