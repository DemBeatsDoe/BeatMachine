<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class ShareController extends Controller
{

    /**
     * @Route("/share/getLink")
     */
    public function getLink(Request $request) {
        $playlistID = $request->request->get('playlistID');

        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->find($playlistID);

        if (!is_null($playlist)) {
            $link = null;
            if (strlen($playlist->getShareCode()) > 1) {
                $code = $playlist->getShareCode();
            } else {
                //Generate new share code
                $code = null;
                while ($code == null) {
                    $code = rand(1000, 1000000);
                    while ($em->getRepository('AppBundle:Playlist')->findOneBy(array('shareCode' => $code)) != null) {
                        $code = rand(1000, 1000000);
                    }
                }
                $playlist->setShareCode($code);
                $em->merge($playlist);
                $em->flush();
            }
            return new JsonResponse(array('success' => true, 'link' => '/share/'.$code));
        }

        return new JsonResponse(array('success' => false));
    }

    /**
     * @Route("/share/{shareCode}")
     */
    public function indexAction($shareCode)
    {
        $em = $this->getDoctrine()->getManager();
        $playlist = $em->getRepository('AppBundle:Playlist')->findOneBy(array('shareCode' => $shareCode));
        if (!is_null($playlist)) {
            //Got our playlist to render
            $r = new Request();
            $r->query->set('id', $playlist->getID());
            return $this->forward('AppBundle:Playlist:index', array('request' => $r, 'editable' => false));
        }

        return $this->render('error.html.twig', array('error' => 'Share does not exist'));
    }
}
