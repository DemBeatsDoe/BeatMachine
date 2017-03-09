<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Song
 *
 * @ORM\Table(name="song")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SongRepository")
 */
class Song
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
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="artist", type="string", length=64)
     */
    private $artist;

    /**
     * @var string
     *
     * @ORM\Column(name="length", type="string", length=8)
     */
    private $length;

    /**
     * @var string
     *
     * @ORM\Column(name="artLink", type="string", length=191)
     */
    private $artLink;

    /**
     * @var string
     *
     * @ORM\Column(name="musicLink", type="string", length=191)
     */
    private $musicLink;


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
     * @return Song
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
     * @return Song
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
     * Set musicLink
     *
     * @param string $musicLink
     *
     * @return Song
     */
    public function setMusicLink($musicLink)
    {
        $this->musicLink = $musicLink;

        return $this;
    }

    /**
     * Get musicLink
     *
     * @return string
     */
    public function getMusicLink()
    {
        return $this->musicLink;
    }

    /**
     * @return string
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @param string $artist
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return string
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param string $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }
}

