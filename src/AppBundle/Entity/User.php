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
     * @ORM\Column(name="likedPlaylists", type="json_array", nullable=true)
     */
    private $likedPlaylists;

    /**
     * @return mixed
     */
    public function getLikedPlaylists()
    {
        return $this->likedPlaylists;
    }

    /**
     * @param mixed $likedPlaylists
     */
    public function setLikedPlaylists($likedPlaylists)
    {
        $this->likedPlaylists = $likedPlaylists;
    }

    public function addLikedPlaylist($id) {
        $arr = $this->getLikedPlaylists();
        array_push($arr, $id);
        $this->setLikedPlaylists($arr);
    }

    public function removeLikedPlaylist($id) {
        $arr = $this->getLikedPlaylists();
        unset($arr[array_search($id, $arr)]);
        $this->setLikedPlaylists(array_values($arr));
    }

    /**
     * @var array
     *
     * @ORM\Column(name="upvotes", type="json_array", nullable=true)
     */
    private $upvotes;

    /**
     * @return array
     */
    public function getDownvotes()
    {
        return $this->downvotes;
    }

    /**
     * @param array $downvotes
     */
    public function setDownvotes($downvotes)
    {
        $this->downvotes = $downvotes;
    }

    /**
     * @return array
     */
    public function getUpvotes()
    {
        return $this->upvotes;
    }

    /**
     * @param array $upvotes
     */
    public function setUpvotes($upvotes)
    {
        $this->upvotes = $upvotes;
    }

    /**
     * @var array
     *
     * @ORM\Column(name="downvotes", type="json_array", nullable=true)
     */
    private $downvotes;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=64)
     */
    private $location = 'Bath, UK';

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

