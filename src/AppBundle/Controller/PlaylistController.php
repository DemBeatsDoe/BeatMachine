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
            'songs' => $songs,
            'editable' => false
        ));
    }


    /**
     * @Route("/playlist/create")
     */
    public function createAction() {
        return $this->render('create_playlist.html.twig');
    }

    /**
     * @Route("/playlist/edit")
     */
    public function editable(Request $request)
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
            'songs' => $songs,
            'editable' => true
        ));
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
     * @Route("/testlogin")
     */
    public function tl() {
        return $this->render('login.html.twig');
    }

    /**
     * @Route("/playlist/changetitle")
     */
    public function updateDB(Request $request)
    {
        $playlistID = $request->request->get('playlistID');
        $name = $request->request->get('name');
        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $this->getUser();
        if ($user->getID() == $playlist->getUserID()) {


            $playlist->setName($name);
            $em->merge($playlist);
            $em->flush();
        }

        return new JsonResponse(array('newTitle' => $playlist->getName()));
    }

    /**
     * @Route("/playlist/changesongname")
     */
    public function updateDBSongName(Request $request)
    {
        $playlistID = $request->request->get('playlistID');
        $songIndex = $request->request->get('songID');
        $name = $request->request->get('name');
        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $this->getUser();
        if ($user->getID() == $playlist->getUserID()) {
            $song = $em->getRepository('AppBundle:Song')->find($playlist->getSongList()[$songIndex]);

            $song->setName($name);

            $em->merge($song);
            $em->flush();
        }

        return new JsonResponse(array('newName' => $song->getName()));
    }

    /**
     * @Route("/playlist/changesongartist")
     */
    public function updateDBSongArtist(Request $request)
    {
        $playlistID = $request->request->get('playlistID');
        $songIndex = $request->request->get('songID');
        $name = $request->request->get('name');
        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $this->getUser();
        if ($user->getID() == $playlist->getUserID()) {
            $song = $em->getRepository('AppBundle:Song')->find($playlist->getSongList()[$songIndex]);

            $song->setArtist($name);

            $em->merge($song);
            $em->flush();
        }

        return new JsonResponse(array('newArtist' => $song->getArtist()));
    }

    /**
     * @Route("/playlist/delete")
     */
    public function delete(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $this->getUser();
        if ($user->getID() == $playlist->getUserID()) {
            $em->remove($playlist);
            $em->flush();
        }

        return new JsonResponse();
    }

    /**
     * @Route("/playlist/addSong")
     */
    public function addSong(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $this->getUser();
        if ($user->getID() == $playlist->getUserID()) {
            $title = $request->request->get('title');
            $artist = $request->request->get('artist');
            $art = $request->request->get('art');
            $url = $request->request->get('url');
            $length = $request->request->get('length');

            $song = new Song();
            $song->setArtist($artist);
            $song->setName($title);
            $song->setMusicLink($u+ '&songID=' + songid + '&name=' + namerl);
            $song->setArtLink($art);
            $song->setLength($length);

            $em->persist($song);
            $em->flush();

            $playlist->addSong($song->getId());
            $em->merge($playlist);
            $em->flush();
            return new JsonResponse(array('success' => true));
        }

        return new JsonResponse(array('success' => false));
    }

    /**
     * @Route("/playlist/removeSong")
     */
    public function removeSong(Request $request) {
        $playlistID = $request->request->get('playlistID');
        $index = $request->request->get('index');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $this->getUser();
        if ($user->getID() == $playlist->getUserID()) {
            $playlist->removeSong($index);
            $em->merge($playlist);
            $em->flush();
        }

        return new JsonResponse();
    }

    /**
     * @Route("/playlist/favourite")
     */
    public function favouritePlaylist(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $em->getRepository('AppBundle:User')->find($this->getUser());;
        if (!is_null($playlist) && !is_null($user)) {
            $user->addLikedPlaylist($playlistID);
            $em->merge($user);
            $em->flush();
        }

        return new JsonResponse();
    }

    /**
     * @Route("/playlist/unfavourite")
     */
    public function unfavouritePlaylist(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $em->getRepository('AppBundle:User')->find($this->getUser());;
        if (!is_null($playlist) && !is_null($user)) {
            $user->removeLikedPlaylist($playlistID);
            $em->merge($user);
            $em->flush();
        }

        return new JsonResponse();
    }
}