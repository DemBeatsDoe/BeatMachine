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

        $loc = "Bath, UK";
        $user = $this->getUser();
        if ($user != null) $loc = $user->getLocation();

        //Get public playlists in order of votes
        $this->playlists = $em->getRepository('AppBundle:Playlist')->findBy(array('isPublic' => '1', 'location' => $loc), array('votes' => 'DESC'), 10);

        //Get array of song art links for each playlist
        $songArt= array();
        $songNames= array();
        $songLinks= array();
        foreach($this->playlists as $playlist) {
            $artarr = array();
            $namearr = array();
            $linkarr = array();
            foreach($playlist->getSongList() as $song) {
                $song = $em->getRepository('AppBundle:Song')->find($song);
                array_push($artarr, $song->getArtLink());
                array_push($namearr, $song->getName());
                array_push($linkarr, $song->getMusicLink());
            }
            array_push($songArt, $artarr);
            array_push($songNames, $namearr);
            array_push($songLinks, $linkarr);
        }

        return $this->render('feed.html.twig', array('playlists' => $this->playlists, 'songArt' => $songArt, 'songNames' => $songNames, 'songLinks' => $songLinks));
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
