<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Playlist;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FeedController extends Controller
{
    private $retreivedPlaylists = false;
    private $playlists = null;
    private $index = 1;


    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        //Check what mode the viewer wants (top, new, etc)
        $mode = $request->query->get('mode');
        if(is_null($mode)){
            $mode = "top";
        }

        //Get DB entity manager
        $em = $this->getDoctrine()->getManager();

        //Check if the user has requested a particular location
        $loc = $request->query->get('loc');
        if(is_null($loc)){
            $loc = "Bath, UK";
            $user = $this->getUser();
            if ($user != null) $loc = $user->getLocation();
        }

        //Get public playlists in order of votes
        $this->playlists = array();
        if($mode == "new"){
            $temp = $em->getRepository('AppBundle:Playlist')->findBy(array('isPublic' => '1', 'location' => $loc), array('id' => 'DESC'), 10);
            foreach ($temp as $p) {
                if (sizeof($p->getSongList()) != 0) array_push($this->playlists, $p);
            }
        }
        else if ($mode == "favourites") {
            $ids = $em->getRepository('AppBundle:User')->find($this->getUser())->getLikedPlaylists();
            $array = array();
            foreach ($ids as $i) {
                array_push($array, $em->getRepository('AppBundle:Playlist')->find($i));
            }
            $this->playlists = $array;
        } else {
            $temp = $em->getRepository('AppBundle:Playlist')->findBy(array('isPublic' => '1', 'location' => $loc), array('votes' => 'DESC'), 10);
            foreach ($temp as $p) {
                if (sizeof($p->getSongList()) != 0) array_push($this->playlists, $p);
            }
        }

        //Get array of song art links for each playlist
        $songArt= array();
        $songNames= array();
        $songArtists = array();
        $songLinks= array();
        foreach($this->playlists as $playlist) {
            $artarr = array();
            $namearr = array();
            $linkarr = array();
            $artistArr = array();
            foreach($playlist->getSongList() as $song) {
                $song = $em->getRepository('AppBundle:Song')->find($song);
                array_push($artarr, $song->getArtLink());
                array_push($namearr, $song->getName());
                array_push($linkarr, $song->getMusicLink());
                array_push($artistArr, $song->getArtist());
            }
            array_push($songArt, $artarr);
            array_push($songNames, $namearr);
            array_push($songLinks, $linkarr);
            array_push($songArtists, $artistArr);
        }

        return $this->render('feed.html.twig', array(
            'location' => $loc,
            'playlists' => $this->playlists,
            'songArt' => $songArt,
            'songNames'=> $songNames,
            'songLinks' => $songLinks,
            'songArtists' => $songArtists,
            'mode' => $mode
        ));
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
