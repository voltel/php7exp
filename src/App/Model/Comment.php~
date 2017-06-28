<?php

namespace App\Model;

/**
 * Comment
 */
class Comment
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \DateTime
     */
    private $posted_at;

    /**
     * @var \App\Model\Post
     */
    private $post;

    /**
     * @var \App\Model\user
     */
    private $user;


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
     * Set comment
     *
     * @param string $comment
     *
     * @return Comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set postedAt
     *
     * @param \DateTime $postedAt
     *
     * @return Comment
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
     * Set post
     *
     * @param \App\Model\Post $post
     *
     * @return Comment
     */
    public function setPost(\App\Model\Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \App\Model\Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set user
     *
     * @param \App\Model\user $user
     *
     * @return Comment
     */
    public function setUser(\App\Model\user $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Model\user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *  Этот метод создан руками и должен быть скопирован после обновления
     * D:\WebSites\php7exp.loc>vendor\bin\doctrine orm:generate:entities src/
     */
    public function getPostedAtFormatted() : string
    {
      $o_date = $this->getPostedAt();
      return $o_date->format("Y-m-d H:i:s");
    }//end of fucntion

}
