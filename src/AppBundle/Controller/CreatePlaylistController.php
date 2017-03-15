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

        $playlist = $em->getRepository('AppBundle:Playlist')->find(id);

        $playlist = new Playlist();
        $playlist->setName('Test');
        $playlist->setUserID(2);
        $playlist->setIsPublic(true);
        $playlist->setVotes(0);
        $playlist->setLocation('Bath, UK');
        $playlist->setArtLink('https://www.theedgesusu.co.uk/wp-content/uploads/2017/03/Alt-J.jpg');
        $playlist->setSongList([]);


        $em->merge($playlist);
        $em->flush();

        return $this->render('createPlaylist.html.twig');
    }
}

