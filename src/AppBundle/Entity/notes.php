<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * notes
 *
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\notesRepository")
 */
class notes
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
     * @Assert\NotBlank(
     *     message = "The Title is required.",
     * )
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var string
     * @Assert\NotBlank(
     *     message = "The Content is required.",
     * )
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     * @Assert\Date()
     * @ORM\Column(name="note_date", type="datetime")
     */
    private $noteDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="predefined", type="integer", length=255)
     */
    private $predefined;



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
     * Set title
     *
     * @param string $title
     *
     * @return notes
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return notes
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return notes
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set noteDate
     *
     * @param \DateTime $noteDate
     *
     * @return notes
     */
    public function setNoteDate($noteDate)
    {
        $this->noteDate = $noteDate;

        return $this;
    }

    /**
     * Get noteDate
     *
     * @return \DateTime
     */
    public function getNoteDate()
    {
        return $this->noteDate;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     *
     * @return notes
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set predefined
     *
     * @param integer $predefined
     *
     * @return notes
     */
    public function setPredefined($predefined)
    {
        $this->predefined = $predefined;

        return $this;
    }

    /**
     * Get predefined
     *
     * @return integer
     */
    public function getPredefined()
    {
        return $this->predefined;
    }
}

