<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlaylistController extends Controller
{
    /**
     * @Route("/playlist")
     */
    public function indexAction(Request $request)
    {
        //Store the playlist id
        $playlistID = $request->query->get('id');

        $playlistSongs = array("Song1", "Song2", "Song3");

        return $this->render('users_playlist.html.twig', array(
            'playlistID' => $playlistID,
            'playlistName' => 'Test Playlist',
            'playlistAuthor' => 'User1',
            'authorID' => 100,
            'playlistSongs' => $playlistSongs,
        ));
    }
}