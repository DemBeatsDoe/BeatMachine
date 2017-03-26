<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Playlist;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class CreatePlaylistController extends Controller
{
    /**
     * @Route("/createPlaylist")
     *
     */
    public function indexAction()
    {
        $playlists = array();
        return $this->render('createPlaylist.html.twig', [
            'playlists' => $playlists
        ]);
    }

    /**
     * @Route("/callCreatePlaylist")
     *
     */
    public function createPlaylist(Request $request)
    {

        //Store the user id
        $userID = $request->request->get('userID');

        $playlistName = $request->request->get('name');
        $em = $this->getDoctrine()->getManager();

        $playlist = $em->getRepository('AppBundle:Playlist')->find(id);

        //Get user from DB
        $user = $em->getRepository('AppBundle:User')->find($userID);
        if ($user == null) return $this->render('error.html.twig', array('error' => "Couldn't find user: ".$userID));

        $playlist = new Playlist();
        $playlist->setName($playlistName);
        $playlist->setUserID($userID);
        $playlist->setIsPublic(true);
        $playlist->setVotes(0);
        $playlist->setLocation('Bath, UK');
        $playlist->setArtLink('https://www.theedgesusu.co.uk/wp-content/uploads/2017/03/Alt-J.jpg');
        $playlist->setSongList([]);

        $em->merge($playlist);
        $em->merge($user);
        $em->flush();

        return new JsonResponse(array(
        //return $this->render('createPlaylist.html.twig', array(
            'user' => $user,
            'playlist' => $playlist
        ));
    }
}

