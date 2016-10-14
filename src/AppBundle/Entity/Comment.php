<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="author_comment", type="string", length=255)
     */
    private $authorComment;

    /**
     * @var string
     *
     * @ORM\Column(name="text_comment", type="text")
     */
    private $textComment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create_comment", type="datetime")
     */
    private $date_create_comment;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments", cascade={"persist"})
     * @ORM\JoinColumn(name="user_comment", referencedColumnName="id")
     */
    private $user_comment;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="post_comment", cascade={"persist"})
     * @ORM\JoinColumn(name="post_comment", referencedColumnName="id")
     */
    private $post_comment;

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
     * Set authorComment
     *
     * @param string $authorComment
     *
     * @return Comment
     */
    public function setAuthorComment($authorComment)
    {
        $this->authorComment = $authorComment;

        return $this;
    }

    /**
     * Get authorComment
     *
     * @return string
     */
    public function getAuthorComment()
    {
        return $this->authorComment;
    }

    /**
     * Set textComment
     *
     * @param string $textComment
     *
     * @return Comment
     */
    public function setTextComment($textComment)
    {
        $this->textComment = $textComment;

        return $this;
    }

    /**
     * Get textComment
     *
     * @return string
     */
    public function getTextComment()
    {
        return $this->textComment;
    }

    /**
     * Set userComment
     *
     * @param \AppBundle\Entity\User $userComment
     *
     * @return Comment
     */
    public function setUserComment(\AppBundle\Entity\User $userComment = null)
    {
        $this->user_comment = $userComment;

        return $this;
    }

    /**
     * Get userComment
     *
     * @return \AppBundle\Entity\User
     */
    public function getUserComment()
    {
        return $this->user_comment;
    }

    /**
     * Set postComment
     *
     * @param \AppBundle\Entity\Post $postComment
     *
     * @return Comment
     */
    public function setPostComment(\AppBundle\Entity\Post $postComment = null)
    {
        $this->post_comment = $postComment;

        return $this;
    }

    /**
     * Get postComment
     *
     * @return \AppBundle\Entity\Post
     */
    public function getPostComment()
    {
        return $this->post_comment;
    }

    /**
     * Set dateCreateComment
     *
     * @param \DateTime $dateCreateComment
     *
     * @return Comment
     */
    public function setDateCreateComment($dateCreateComment)
    {
        $this->date_create_comment = $dateCreateComment;

        return $this;
    }

    /**
     * Get dateCreateComment
     *
     * @return \DateTime
     */
    public function getDateCreateComment()
    {
        return $this->date_create_comment;
    }
}
