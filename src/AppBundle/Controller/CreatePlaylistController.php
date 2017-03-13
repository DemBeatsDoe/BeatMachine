<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class CreatePlaylistController extends Controller
{
    /**
     * @Route("/createPlaylist")
     */
    public function indexAction()
    {
        return $this->render('createPlaylist.html.twig');
    }
}

