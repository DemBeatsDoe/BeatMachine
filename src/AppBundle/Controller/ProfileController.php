<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @Route("/profile")
     */
    public function indexAction(Request $request)
    {
        //Store the user id
        $userID = $request->query->get('id');

        //Get DB Entity Manager
        $em = $this->getDoctrine()->getManager();

        //Get user from DB
        $user = $em->getRepository('AppBundle:User')->find($userID);
        if ($user == null) return $this->render('error.html.twig', array('error' => "Couldn't find user: ".$userID));

        //Get user's playlists
        //$userPlaylists = $em->getRepository('AppBundle:Playlist')->findBy(array('userID' => $userID), array('id'=>'ASC'));
        $allPlaylists = $em->getRepository('AppBundle:Playlist')->findAll(array('id'=>'ASC'));
        $userPlaylists = array();
        foreach ($allPlaylists as $p) {
            if (array_search($userID, $p->getCollaborators()) != false || $userID == $p->getUserID()) {
                if ($p->getIsPublic()) {
                    array_push($userPlaylists, $p);
                } else {
                    $activeUser = $this->getUser();
                    if (!is_null($activeUser)) {
                        if ($activeUser->getID() == $p->getUserID()) array_push($userPlaylists, $p);
                    }
                }
            }
        }

        //Get array of song art links for each playlist
        $songArt= array();
        $songNames= array();
        $songArtists = array();
        $songLinks= array();
        foreach($userPlaylists as $playlist) {
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

        return $this->render('users_profile.html.twig', array(
            'user' =>  $user,
            'playlists' => $userPlaylists,
            'songArt' => $songArt,
            'songNames'=> $songNames,
            'songLinks' => $songLinks,
            'songArtists' => $songArtists
        ));
    }


    /**
     * @Route("/profile/edit/setLocation")
     */
    public function changeLocation(Request $request) {
        $loc = $request->request->get('location');
        $user = $this->getUser();

        if (!is_null($user) && !is_null($loc)) {
            $em = $this->getDoctrine()->getManager();
            $u = $em->getRepository('AppBundle:User')->find($user->getID());
            if (!is_null($u)) {
                $u->setLocation($loc);
                $em->merge($u);
                $em->flush();
                //SUCCESS
                return new JsonResponse(array('success' => true));
            }
        }
        //FAIL
        return new JsonResponse(array('success' => false));
    }
}

