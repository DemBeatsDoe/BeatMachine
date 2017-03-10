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
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=64)
     */
    private $location;

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

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

