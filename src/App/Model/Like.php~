<?php

namespace App\Model;

/**
 * Like
 */
class Like
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $posted_at;

    /**
     * @var \App\Model\User
     */
    private $user;

    /**
     * @var \App\Model\Post
     */
    private $associated_post;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set postedAt
     *
     * @param \DateTime $postedAt
     *
     * @return Like
     */
    public function setPostedAt($postedAt)
    {
        $this->posted_at = $postedAt;

        return $this;
    }

    /**
     * Get postedAt
     *
     * @return \DateTime
     */
    public function getPostedAt()
    {
        return $this->posted_at;
    }

    /**
     * Set user
     *
     * @param \App\Model\User $user
     *
     * @return Like
     */
    public function setUser(\App\Model\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Model\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set associatedPost
     *
     * @param \App\Model\Post $associatedPost
     *
     * @return Like
     */
    public function setAssociatedPost(\App\Model\Post $associatedPost)
    {
        $this->associated_post = $associatedPost;

        return $this;
    }

    /**
     * Get associatedPost
     *
     * @return \App\Model\Post
     */
    public function getAssociatedPost()
    {
        return $this->associated_post;
    }
}
