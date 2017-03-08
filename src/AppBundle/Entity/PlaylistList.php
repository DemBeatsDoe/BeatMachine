<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlaylistList
 *
 * @ORM\Table(name="playlist_list")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaylistListRepository")
 */
class PlaylistList
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
     * @var int
     *
     * @ORM\Column(name="playlistID", type="integer", unique=true)
     */
    private $playlistID;

    /**
     * @var int
     *
     * @ORM\Column(name="playlistScore", type="integer")
     */
    private $playlistScore;


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
     * Set playlistID
     *
     * @param integer $playlistID
     *
     * @return PlaylistList
     */
    public function setPlaylistID($playlistID)
    {
        $this->playlistID = $playlistID;

        return $this;
    }

    /**
     * Get playlistID
     *
     * @return int
     */
    public function getPlaylistID()
    {
        return $this->playlistID;
    }

    /**
     * Set playlistScore
     *
     * @param integer $playlistScore
     *
     * @return PlaylistList
     */
    public function setPlaylistScore($playlistScore)
    {
        $this->playlistScore = $playlistScore;

        return $this;
    }

    /**
     * Get playlistScore
     *
     * @return int
     */
    public function getPlaylistScore()
    {
        return $this->playlistScore;
    }
}

