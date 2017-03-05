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
        $playlistSongs = array("Song1", "Song2", "Song3");

        return $this->render('users_playlist.html.twig', array(
            'playlistName' => 'Test Playlist',
            'playlistAuthor' => 'User1',
            'playlistSongs' => $playlistSongs,
        ));
    }
}
