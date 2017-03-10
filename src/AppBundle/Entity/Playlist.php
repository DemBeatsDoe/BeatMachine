<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Playlist
 *
 * @ORM\Table(name="playlist")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaylistRepository")
 */
class Playlist
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
     * @var bool
     *
     * @ORM\Column(name="isPublic", type="boolean")
     */
    private $isPublic;

    /**
     * @var int
     *
     * @ORM\Column(name="votes", type="integer")
     */
    private $votes;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

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
     * @var string
     *
     * @ORM\Column(name="artLink", type="string", length=191)
     */
    private $artLink;

    /**
     * @var int
     *
     * @ORM\Column(name="userID", type="integer", unique=false)
     */
    private $userID;

    /**
     * @var array
     *
     * @ORM\Column(name="songList", type="json_array")
     */
    private $songList;

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
     * Set name
     *
     * @param string $name
     *
     * @return Playlist
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set artLink
     *
     * @param string $artLink
     *
     * @return Playlist
     */
    public function setArtLink($artLink)
    {
        $this->artLink = $artLink;

        return $this;
    }

    /**
     * Get artLink
     *
     * @return string
     */
    public function getArtLink()
    {
        return $this->artLink;
    }

    /**
     * Set userID
     *
     * @param integer $userID
     *
     * @return Playlist
     */
    public function setUserID($userID)
    {
        $this->userID = $userID;

        return $this;
    }

    /**
     * Get userID
     *
     * @return int
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * @return array
     */
    public function getSongList()
    {
        return $this->songList;
    }

    /**
     * @param array $songList
     */
    public function setSongList($songList)
    {
        $this->songList = $songList;
    }

    /**
     * @return int
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param int $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    public function voteUp()
    {
        $this->votes++;
    }

    public function voteDown()
    {
        $this->votes--;
    }

    /**
     * @return mixed
     */
    public function getIsPublic()
    {
        return $this->isPublic;
    }

    /**
     * @param mixed $isPublic
     */
    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
    }
}

