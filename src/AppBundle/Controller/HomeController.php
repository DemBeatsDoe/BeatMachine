<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('playlist.html.twig');
    }

    /**
     * @Route("/getNextPlaylist", name="requestNextPlaylistForFeed")
     */
    public function processAJAX()
    {
        //temp
        $verbList = array("Chilled", "Relaxed", "Hardcore", "Epic", "Silent", "Smooth", "Electric", "Rowdy", "Saucy", "Extreme", "Insane", "Evening", "Morning");
        $nounList = array("Rock", "Jazz", "House", "Grime", "Songs", "Ballads", "Country", "Acoustic", "Acapella", "EDM", "Indie", "Bangers", "Hip Hop", "Disco", "Punk", "Death Metal", "Vibes");
        $albumArt = array("1.png", "2.png", "3.jpg", "4.jpg", "5.jpg", "6.jpg", "7.jpg", "8.png", "9.jpg", "10.jpg", "11.jpg", "12.jpg", "13.jpg", "14.png", "15.jpg", "16.jpg", "17.jpg", "18.jpg", "19.jpg", "20.jpg");

        //Playlist Properties:
        $playlistName = $verbList[array_rand($verbList)] . " " . $nounList[array_rand($nounList)];
        $playlistArt = $albumArt[array_rand($albumArt)];
        $numLikes = rand(0, 120);
        $songArt = array();

        //temp fill song array with random songs
        $arrLen = rand(2, 6);
        for ($i = 0; $i < $arrLen; $i++)
        {
            array_push($songArt, $albumArt[array_rand($albumArt)]);
        }

        //Return a response containing the information about this playlist
        $response = new JsonResponse(array(
            'name' => $playlistName,
            'art' => $playlistArt,
            'likeCount' => $numLikes,
            'songs' => $songArt));

        return $response;
    }
}
