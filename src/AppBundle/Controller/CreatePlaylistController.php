<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Playlist;
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
        $em = $this->getDoctrine()->getManager();


        $playlist = new Playlist();
        $playlist->setName('Test');
        $playlist->setUserID(2);

        $em->merge($playlist);
        $em->flush();

        return $this->render('createPlaylist.html.twig');
    }
}

