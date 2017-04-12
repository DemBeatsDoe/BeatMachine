<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Song;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PlaylistController extends Controller
{
    /**
     * @Route("/playlist", defaults={"editable": false})
     */
    public function indexAction(Request $request, $editable)
    {
        //Store the playlist id
        $playlistID = $request->query->get('id');
        if (is_null($playlistID)) return $this->render('error.html.twig', array('error' => 'No playlist ID specified'));

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

        //Get collaborators as user objects
        $cids = $playlist->getCollaborators();
        $collaborators = array();
        foreach ($cids as $c) {
            $u = $em->getRepository('AppBundle:User')->find($c);
            if (!is_null($u)) array_push($collaborators, $u);
        }

        return $this->render('users_playlist.html.twig', array(
            'playlistArt' => $playlist->getArtLink(),
            'playlistID' => $playlistID,
            'playlistName' => $playlist->getName(),
            'playlistAuthor' => $user->getUsername(),
            'authorID' => $playlist->getUserID(),
            'songs' => $songs,
            'collaborators' => $collaborators,
            'editMode' => $editable,
            'canUserEdit' => $this->canUserEdit($playlistID, $this->getUser()),
            'public' => $playlist->getIsPublic()
        ));
    }

    /**
     * @Route("/playlist/edit/collaborators")
     */
    public function playlistEditCollaborators(Request $request) {
        //Store the playlist id
        $playlistID = $request->query->get('id');

        $em = $this->getDoctrine()->getManager();

        //Get playlist + user data from DB
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);
        if ($playlist == null) return $this->render('error.html.twig', array('error' => "Couldn't find playlist: ".$playlistID));

        //Get collab array
        $collaborators = array();
        foreach ($playlist->getCollaborators() as $i) {
            $user = $em->getRepository('AppBundle:User')->find($i);
            if (!is_null($user)) array_push($collaborators, $user);
        }

        return $this->render('playlist_edit_collaborators.html.twig', array('playlist' => $playlist, 'collaborators' => $collaborators));
    }

    /**
     * @Route("/playlist/edit/addCollaborator")
     */
    public function addCollaborator(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $em->getRepository('AppBundle:User')->find($this->getUser());;
        if (!is_null($playlist) && !is_null($user)) {
            if ($playlist->getUserID() == $user->getId()) {
                //Find user based on input from form
                $input = $request->request->get('input');
                $collaborator = $em->getRepository('AppBundle:User')->findOneBy(array('username'=>$input));
                if (is_null($collaborator)) $collaborator = $em->getRepository('AppBundle:User')->findOneBy(array('email'=>$input));
                if (!is_null($collaborator)) {
                    if (array_search($collaborator->getId(), $playlist->getCollaborators()) == false) { //Check it isn't already in the array
                        $playlist->addCollaborator($collaborator->getId());
                        $em->merge($playlist);
                        $em->flush();
                        return new JsonResponse(array('success' => true, 'username' => $collaborator->getUsername(), 'userID' => $collaborator->getId()));
                    }
                }
            }
        }
        return new JsonResponse(array('success' => false));
    }

    /**
     * @Route("/playlist/edit/removeCollaborator")
     */
    public function removeCollaborator(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $em->getRepository('AppBundle:User')->find($this->getUser());;
        if (!is_null($playlist) && !is_null($user)) {
            if ($playlist->getUserID() == $user->getId()) {
                //Remove collaborator from playlist
                $playlist->removeCollaborator($request->request->get('collaboratorID'));
                $em->merge($playlist);
                $em->flush();
                return new JsonResponse(array('success' => true));
            }
        }
        return new JsonResponse(array('success' => false));
    }

    private function canUserEdit($playlistID, $user) {
        $em = $this->getDoctrine()->getManager();

        //Get playlist + user data from DB
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        //Check user ID

        if (is_null($user)) { //Not logged in
            if ($playlist->getIsPublic()) {
                return false;
            } else {
                return true; //Editable with link
            }
        }
        if ($user->getID() == $playlist->getUserID()) return true; //Owner
        if (in_array($user->getID(), $playlist->getCollaborators())) return true; //Collaborator
    }

    /**
     * @Route("/playlist/edit")
     */
    public function editable(Request $request)
    {
        //Store the playlist id
        $playlistID = $request->query->get('id');
        if (is_null($playlistID)) return $this->render('error.html.twig', array('error' => 'No playlist ID specified'));

        $em = $this->getDoctrine()->getManager();

        //Get playlist + user data from DB
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);
        if ($playlist == null) return $this->render('error.html.twig', array('error' => "Couldn't find playlist: ".$playlistID));

        //Get current user
        $userID = $this->getUser();

        return $this->indexAction($request, $this->canUserEdit($playlistID, $userID));
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

        if ($this->canUserEdit($playlistID, $this->getUser())) {
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

        if ($this->canUserEdit($playlistID, $this->getUser())) {
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

        if ($this->canUserEdit($playlistID, $this->getUser())) {
            $song = $em->getRepository('AppBundle:Song')->find($playlist->getSongList()[$songIndex]);

            $song->setArtist($name);

            $em->merge($song);
            $em->flush();
        }

        return new JsonResponse(array('newArtist' => $song->getArtist()));
    }

    /**
     * @Route("/playlist/togglePrivate")
     */
    public function togglePrivate(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $this->getUser();
        if ($user->getID() == $playlist->getUserID()) {
            if ($playlist->getIsPublic()) {
                $playlist->setIsPublic(false);
            } else {
                $playlist->setIsPublic(true);
            }

            $em->merge($playlist);
            $em->flush();
            return new JsonResponse(array('success' => true));
        }

        return new JsonResponse(array('success' => false));
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

        if ($this->canUserEdit($playlistID, $this->getUser())) {

            $url = $request->request->get('url');
            $song = $em->getRepository('AppBundle:Song')->findOneBy(array('musicLink' => $url));
            if (is_null($song)) {
                $title = $request->request->get('title');
                $artist = $request->request->get('artist');
                $art = $request->request->get('art');
                $length = $request->request->get('length');

                $song = new Song();
                $song->setArtist($artist);
                $song->setName($title);
                $song->setMusicLink($url);
                $song->setArtLink($art);
                $song->setLength($length);

                $em->persist($song);
                $em->flush();
            }

            if (array_search($song->getId(), $playlist->getSongList()) != false) return new JsonResponse(array('success' => false, 'error' => 'Song already in playlist'));
            $playlist->addSong($song->getId());
            $em->merge($playlist);
            $em->flush();
            return new JsonResponse(array('success' => true, 'id' => $song->getId(), 'title' => $song->getName(), 'artist' => $song->getArtist(), 'length' => $song->getLength()));
        }

        return new JsonResponse(array('success' => false, 'error' => 'Unable to add song'));
    }

    /**
     * @Route("/playlist/removeSong")
     */
    public function removeSong(Request $request) {
        $playlistID = $request->request->get('playlistID');
        $songID = $request->request->get('songID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        if ($this->canUserEdit($playlistID, $this->getUser())) {

            $playlist->removeSong($songID);
            $em->merge($playlist);
            $em->flush();
            return new JsonResponse(array('success' => true));
        }

        return new JsonResponse(array('success' => false));
    }

    /**
     * @Route("/playlist/toggleFavourite")
     */
    public function favouritePlaylist(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        $user = $em->getRepository('AppBundle:User')->find($this->getUser());;
        if (!is_null($playlist) && !is_null($user)) {
            if (!in_array($playlistID, $user->getLikedPlaylists())) {
                $user->addLikedPlaylist($playlistID);
            } else {
                $user->removeLikedPlaylist($playlistID);
            }
            $em->merge($user);
            $em->flush();
            return new JsonResponse(array('success' => true));
        }

        return new JsonResponse(array('success' => false));
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

        return new JsonResponse(array());
    }
}