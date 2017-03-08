<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=64, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=191, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="passHash", type="string", length=100)
     */
    private $passHash;


    /**
     * @var array
     *
     * @ORM\Column(name="userPlaylists", type="json_array")
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

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set passHash
     *
     * @param string $passHash
     *
     * @return User
     */
    public function setPassHash($passHash)
    {
        $this->passHash = $passHash;

        return $this;
    }

    /**
     * Get passHash
     *
     * @return string
     */
    public function getPassHash()
    {
        return $this->passHash;
    }


}

