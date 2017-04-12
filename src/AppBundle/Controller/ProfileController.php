<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        $songLinks = array();
        foreach($userPlaylists as $playlist) {
            $linkarr = array();
            foreach($playlist->getSongList() as $song) {
                array_push($linkarr, $em->getRepository('AppBundle:Song')->find($song)->getArtLink());
            }
            array_push($songLinks, $linkarr);
        }

        return $this->render('users_profile.html.twig', array(
            'user' =>  $user,
            'playlists' => $userPlaylists,
            'songLinks' => $songLinks
        ));
    }


}

