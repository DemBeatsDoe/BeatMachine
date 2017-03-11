<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Song;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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


    /**
     * @Route("/playlist/create")
     */
    public function createAction() {
        return $this->render('create_playlist.html.twig');
    }


    /**
     * @Route("/playlist/vote")
     */
    public function processAJAX(Request $request)
    {
        $playlistID = $request->request->get('playlistID');
        $voteDirection = $request->request->get('direction');

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $ups = $user->getUpvotes();
        $downs = $user->getDownvotes();
        $votecount = 0;
        if ($voteDirection == 'up') {
            if (!in_array($playlistID, $ups)) { //Check the user hasn't already voted in this direction
                if (in_array($playlistID, $downs)) {
                    unset($downs[array_search($playlistID, $downs)]);
                    $votecount++;
                }
                array_push($ups, $playlistID);
                $votecount++;
            }
        } else if ($voteDirection == 'down') {
            if (!in_array($playlistID, $downs)) {
                if (in_array($playlistID, $ups)) {
                    unset($ups[array_search($playlistID, $ups)]);
                    $votecount--;
                }
                array_push($downs, $playlistID);
                $votecount--;
            }
        }

        $user->setUpvotes($ups);
        $user->setDownvotes($downs);
        $playlist->addVotes($votecount);
        $em->merge($playlist);
        $em->merge($user);
        $em->flush();

        return new JsonResponse(array('votes' => $playlist->getVotes()));
    }

    /**
     * @Route("/tl")
     */
    public function tl() {
        return $this->render('login.html.twig');
    }
}