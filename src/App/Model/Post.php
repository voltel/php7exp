<?php

namespace App\Model;

/**
 * Post
 */
class Post
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $image;

    /**
     * @var \DateTime
     */
    private $posted_at;

    /**
     * @var integer
     */
    private $comments_num = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $associated_likes;

    /**
     * @var \App\Model\User
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->associated_likes = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     *
     * @return Post
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
     * Set description
     *
     * @param string $description
     *
     * @return Post
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Post
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set postedAt
     *
     * @param \DateTime $postedAt
     *
     * @return Post
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
     * Set commentsNum
     *
     * @param integer $commentsNum
     *
     * @return Post
     */
    public function setCommentsNum($commentsNum)
    {
        $this->comments_num = $commentsNum;

        return $this;
    }

    /**
     * Get commentsNum
     *
     * @return integer
     */
    public function getCommentsNum()
    {
        return $this->comments_num;
    }

    /**
     * Add associatedLike
     *
     * @param \App\Model\Like $associatedLike
     *
     * @return Post
     */
    public function addAssociatedLike(\App\Model\Like $associatedLike)
    {
        $this->associated_likes[] = $associatedLike;

        return $this;
    }

    /**
     * Remove associatedLike
     *
     * @param \App\Model\Like $associatedLike
     */
    public function removeAssociatedLike(\App\Model\Like $associatedLike)
    {
        $this->associated_likes->removeElement($associatedLike);
    }

    /**
     * Get associatedLikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAssociatedLikes()
    {
        return $this->associated_likes;
    }

    /**
     * Set user
     *
     * @param \App\Model\User $user
     *
     * @return Post
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
     * Написано вручную
     */
    public function isLikedByUser(User $user) : bool
    {
      $a_associated_likes = $this->getAssociatedLikes();
      if (count($a_associated_likes) == 0) return false;

      $a_this_user_likes = $a_associated_likes->filter(function($like) use ($user) {
          return ($like->getUser()->getId() == $user->getId());
      });

      return count($a_this_user_likes) > 0;
    }//end of function

    /**
     * Написано вручную
     */
     public function getNumLikes() : int
     {
       return count($this->getAssociatedLikes());
     }//end of funciton

}
