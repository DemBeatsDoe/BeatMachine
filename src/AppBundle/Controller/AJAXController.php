<?php
/**
 * Created by PhpStorm.
 * User: alistair
 * Date: 28/02/17
 * Time: 17:48
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class AJAXController extends Controller
{
    /**
     * @Route("/ajaxtest")
     */
    public function indexAction()
    {
        return $this->render('ajaxTest.html.twig');
    }

    /**
     * @Route("/call", name="receiptFetchSystem")
     * @Template()
     */
    public function callAction()
    {
        //$request = $this->container->get('request');

        //$items = $request->request->get('items');
        $items = ["hi", "yo", "m8"];
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        //$itemsList = preg_split('/\r\n|[\r\n]/', $items);

        $response->setContent(json_encode($items));
        return $response;
    }
}
?>