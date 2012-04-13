<?php

/**
  *  Bloginy, Blog Aggregator
  *  Copyright (C) 2012  Riad Benguella - Rizeway
  *
  *  This program is free software: you can redistribute it and/or modify
  *
  *  it under the terms of the GNU General Public License as published by
  *  the Free Software Foundation, either version 3 of the License, or
  *  any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
  *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  * GNU General Public License for more details.
  *
  *  You should have received a copy of the GNU General Public License
  *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Rizeway\BloginyBundle\Entity;

/**
 * Rizeway\BloginyBundle\Entity\Activity
 */
class Activity
{
    const TYPE_COMMENT_CREATION = 'new_comment';
    const TYPE_POST_CREATION    = 'new_post';
    const TYPE_PAGE_CREATION    = 'new_page';
    const TYPE_BLOG_CREATION    = 'new_blog';
    const TYPE_USER_CREATION    = 'new_user';
    const TYPE_VOTE             = 'new_vote';
   
    /**
     * @var string $type
     */
    private $type;

    /**
     * @var boolean $approved
     */
    private $approved = true;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \Rizeway\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var Rizeway\BloginyBundle\Entity\Blog
     */
    private $blog;

    /**
     * @var Rizeway\BloginyBundle\Entity\Comment
     */
    private $comment;

    /**
     * @var Rizeway\BloginyBundle\Entity\Post
     */
    private $post;

    public function  __construct()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string $type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * Get approved
     *
     * @return boolean $approved
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \Rizeway\UserBundle\Entity\User $user
     */
    public function setUser(\Rizeway\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return \Rizeway\UserBundle\Entity\User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set blog
     *
     * @param Rizeway\BloginyBundle\Entity\Blog $blog
     */
    public function setBlog(\Rizeway\BloginyBundle\Entity\Blog $blog)
    {
        $this->blog = $blog;
    }

    /**
     * Get blog
     *
     * @return Rizeway\BloginyBundle\Entity\Blog $blog
     */
    public function getBlog()
    {
        return $this->blog;
    }

    /**
     * Set comment
     *
     * @param Rizeway\BloginyBundle\Entity\Comment $comment
     */
    public function setComment(\Rizeway\BloginyBundle\Entity\Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return Rizeway\BloginyBundle\Entity\Comment $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set post
     *
     * @param Rizeway\BloginyBundle\Entity\Post $post
     */
    public function setPost(\Rizeway\BloginyBundle\Entity\Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get post
     *
     * @return Rizeway\BloginyBundle\Entity\Post $post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @var Rizeway\BloginyBundle\Entity\Page
     */
    private $page;


    /**
     * Set page
     *
     * @param Rizeway\BloginyBundle\Entity\Page $page
     */
    public function setPage(\Rizeway\BloginyBundle\Entity\Page $page)
    {
        $this->page = $page;
    }

    /**
     * Get page
     *
     * @return Rizeway\BloginyBundle\Entity\Page 
     */
    public function getPage()
    {
        return $this->page;
    }
}