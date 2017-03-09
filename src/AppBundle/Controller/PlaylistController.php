<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Song;
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
        if ($user == null) return $this->render('error.html.twig', array('error' => "Couldn't find playlist user: ".$playlist->getUserID()));

        //This makes a test playlist and songs and pushes it to the DB
        /*
        //Make playlist that refrences songs 1, 2, 3 and push to DB
        $testList = array(1, 2, 3);

        $playlist->setSongList($testList);
        $em->merge($playlist);

        //Make 3 songs and push to DB
        $song1 = new Song();
        $song1->setName('All Star Dance Remix');
        $song1->setMusicLink('http://api.soundcloud.com/tracks/168711691');
        $song1->setArtLink('http://www.drodd.com/images16/album-art18.png');
        $song2 = new Song();
        $song2->setName('All Star For A Little Bit');
        $song2->setMusicLink('http://api.soundcloud.com/tracks/253185930');
        $song2->setArtLink('http://www.drodd.com/images16/album-art18.png');
        $song3 = new Song();
        $song3->setName('All Star Forever');
        $song3->setMusicLink('http://api.soundcloud.com/tracks/239716261');
        $song3->setArtLink('http://www.drodd.com/images16/album-art18.png');
        $em->merge($song1);
        $em->merge($song2);
        $em->merge($song3);
        $em->flush();
        */

        //Get array of song entities from array of song IDs
        $songs = array();
        $songList = $playlist->getSongList();
        foreach($songList as $i) {
            array_push($songs, $em->getRepository('AppBundle:Song')->find($i));
        }

        return $this->render('users_playlist.html.twig', array(
            'playlistArt' => $playlist->getArtLink(),
            'playlistID' => $playlistID,
            'playlistName' => $playlist->getName(),
            'playlistAuthor' => $user->getUsername(),
            'authorID' => $playlist->getUserID(),
            'songs' => $songs
        ));
    }
}