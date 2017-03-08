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

        //Get user playlists as an array
        $userPlaylistArray = array();
        $playlistIDs = $user->getPlaylistIDs();
        foreach ($playlistIDs as $pid) {
            //Get playlist from DB
            $p = $em->getRepository('AppBundle:Playlist')->find($pid);
            if ($user == null) return $this->render('error.html.twig', array('error' => "Couldn't find user playlist: ".$pid));
            array_push($userPlaylistArray, array($p->getId(), $p->getName(), $p->getArtLink()));
        }


        //This makes a test user playlist array and pushes it to the DB
        /*
        $testList = array(
            1
        );

        $user->setPlaylistIDs($testList);
        $em->merge($user);
        $em->flush();
        */

        return $this->render('users_profile.html.twig', array(
            'userName' =>  $user->getUsername(),
            'playlists' => $userPlaylistArray
        ));
    }
}