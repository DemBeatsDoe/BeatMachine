<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class FeedController extends Controller
{
    private $retreivedPlaylists = false;
    private $playlists = null;
    private $index = 1;


    /**
     * @Route("/")
     */
    public function indexAction()
    {
        //Get DB entity manager
        $em = $this->getDoctrine()->getManager();

        //Get public playlists in order of votes
        $this->playlists = $em->getRepository('AppBundle:Playlist')->findAll(); //, array('votes' => 'DEC')

        /*
        //Get first n
        $i = 0;
        $n = sizeof($this->playlists);
        $art = array();
        $name = array();
        $votes = array();
        while ($i < $n) {
            array_push($art, $this->playlists[$i]->getArtLink());
            array_push($name, $this->playlists[$i]->getName());
            array_push($votes, $this->playlists[$i]->getVotes());
            $i++;
        }
        */
/*
        //Get array of song links for each playlist
        $songs = array();
        foreach($this->playlists as $playlist) {
            $linkarr = array();
            foreach($playlist->getSongList() as $song) {
                array_push($linkarr, $em->getRepository('AppBundle:Song')->find($song)->getArtLink());
            }
            array_push($songs, $linkarr);
        }
*/
        return $this->render('playlist.html.twig', array('playlists' => $this->playlists));
    }

    /**
     * @Route("/getNextPlaylist", name="requestNextPlaylistForFeed")
     */
    public function processAJAX()
    {
/*
        if ($this->index == -1) { //This is set to -1 when all playlists have been sent
            return new JsonResponse(-1);
        }

        //Get next playlist
        if ($this->playlists != null) {
            $i = $this->playlists[$this->index];
            $this->index++;
            if ($this->index >= sizeof($this->playlists)) $this->index = -1;
        } else {
            return $this->render('error.html.twig', array('error' => "Playlists were not loaded from DB"));
        }


        //Return a response containing the information about this playlist
        $response = new JsonResponse(array(
            'name' => $i->getName(),
            'art' => $i->getArtLink(),
            'likeCount' => $i->getVotes(),
            'songs' => ''));

        return $response;
*/
    }
}
