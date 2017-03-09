<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends \FOS\UserBundle\Model\User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @var array
     *
     * @ORM\Column(name="userPlaylists", type="json_array", nullable=true)
     */
    private $playlistIDs;

    /**
     * @return mixed
     */
    public function getPlaylistIDs()
    {
        return $this->playlistIDs;
    }

    /**
     * @param mixed $playlistIDs
     */
    public function setPlaylistIDs($playlistIDs)
    {
        $this->playlistIDs = $playlistIDs;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

