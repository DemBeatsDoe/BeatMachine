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
        //Store the playlist id
        $playlistID = $request->query->get('id');

        return $this->render('users_profile.html.twig', array(
            'userName' =>  'Test User'
        ));
    }
}