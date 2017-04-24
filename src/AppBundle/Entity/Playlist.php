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
     * @return string
     */
    public function getShareCode(): string
    {
        return $this->shareCode;
    }

    /**
     * @param string $shareCode
     */
    public function setShareCode(string $shareCode)
    {
        $this->shareCode = $shareCode;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="shareCode", type="string", length=12, nullable=true)
     */
    private $shareCode;

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
     * @var array
     *
     * @ORM\Column(name="collaborators", type="json_array")
     */
    private $collaborators;

    /**
     * @return array
     */
    public function getCollaborators(): array
    {
        return $this->collaborators;
    }

    /**
     * @param array $collaborators
     */
    public function setCollaborators(array $collaborators)
    {
        $this->collaborators = $collaborators;
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

    public function addVotes($n)
    {
        $this->votes += $n;
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


    public function addSong($id)
    {
        if (array_search($id, $this->songList) == false) {
            $arr = $this->getSongList();
            array_push($arr, $id);
            $this->setSongList($arr);
        }
    }

    public function removeSong($id)
    {
        $arr = $this->getSongList();
        unset($arr[array_search($id, $arr)]);
        $this->setSongList(array_values($arr));
    }

    public function addCollaborator($id)
    {
        $arr = $this->collaborators;
        array_push($arr, $id);
        $this->collaborators = $arr;
    }

    public function removeCollaborator($id)
    {
        $arr = $this->collaborators;
        unset($arr[array_search($id, $arr)]);
        $this->collaborators = array_values($arr);
    }
}
