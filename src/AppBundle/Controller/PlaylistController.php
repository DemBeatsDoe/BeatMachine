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

        $em = $this->getDoctrine()->getManager();

        //Get playlist + user data from DB
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);
        if ($playlist == null) return $this->render('error.html.twig', array('error' => "Couldn't find playlist: ".$playlistID));

        $user = $em->getRepository('AppBundle:User')->find($playlist->getUserId());
        if ($playlist == null) return $this->render('error.html.twig', array('error' => "Couldn't find playlist user: ".$playlist->getUserID()));

        //This makes a test song array and pushes it to the DB
        /*
        $testList = array(
            array(
                2.31,
                "Test name",
                "Test artist"
            ),
            array(
                2.31,
                "Test name",
                "Test artist"
            ),
                array(
                    2.31,
                    "Test name",
                    "Test artist"
                )
        );

        $playlist->setSongList($testList);
        $em->merge($playlist);
        $em->flush();
        */

        return $this->render('users_playlist.html.twig', array(
            'playlistArt' => $playlist->getArtLink(),
            'playlistID' => $playlistID,
            'playlistName' => $playlist->getName(),
            'playlistAuthor' => $user->getUsername(),
            'authorID' => $playlist->getUserID(),
            'playlistSongs' => $playlist->getSongList(),
        ));
    }
}