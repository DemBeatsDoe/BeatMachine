<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 04/03/2017
 * Time: 20:57
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bridge\Doctrine;

class DBTestController
{
    /**
     * @Route("/dbtest")
     */
    function save()
    {
        $user = new User();
        $user->setUsername('TestName');
        $user->setEmail('TestEmail');
        $user->setPassHash('TestHash');

        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the User (no queries yet)
        $em->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return new Response('Saved new product with id '.$user->getId());
    }

}