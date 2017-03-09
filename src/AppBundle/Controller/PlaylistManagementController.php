<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Song;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlaylistManagementController extends Controller
{
    /**
     * @Route("/playlist/manage")
     *
     */
    public function indexAction(Request $request)
    {
        $id = $request->query->get('id');

        return $this->render('playlist_management.html.twig', array('playlistID' => $id));
    }
}
