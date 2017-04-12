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
        return $this->render('createPlaylist.html.twig', [
        ]);
    }

    /**
     * @Route("/callCreatePlaylist")
     *
     */
    public function createPlaylist(Request $request)
    {
        //Store the user id
        $user = $this->getUser();

        if (!is_null($user)) {
            $userID = $user->getID();
            $playlistName = $request->request->get('name');
            $em = $this->getDoctrine()->getManager();

            $playlist = new Playlist();
            $playlist->setName($playlistName);
            $playlist->setUserID($userID);
            $playlist->setIsPublic(true);
            $playlist->setVotes(0);
            $playlist->setLocation('Bath, UK');
            $playlist->setArtLink('https://www.theedgesusu.co.uk/wp-content/uploads/2017/03/Alt-J.jpg');
            $playlist->setSongList([]);
            $playlist->setCollaborators([]);


            $em->persist($playlist);
            $em->flush();

            $playlistID = $playlist->getId();

            return new JsonResponse(array('success' => true, 'playlistID' => $playlistID));
        }
        return new JsonResponse(array('success' => false));
    }
}

