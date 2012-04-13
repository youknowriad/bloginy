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
 * Rizeway\BloginyBundle\Entity\Comment
 */
class Comment
{
    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $web_site
     */
    private $web_site;

    /**
     * @var text $comment
     */
    private $comment;

    /**
     * @var boolean $approved
     */
    private $approved = false;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Rizeway\BloginyBundle\Entity\User
     */
    private $user;

    /**
     * @var Rizeway\BloginyBundle\Entity\Post
     */
    private $post;
    
    public $captcha;

    public function  __construct()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set web_site
     *
     * @param string $webSite
     */
    public function setWebSite($webSite)
    {
        $this->web_site = $webSite;
    }

    /**
     * Get web_site
     *
     * @return string $webSite
     */
    public function getWebSite()
    {
        return $this->web_site;
    }

    /**
     * Set comment
     *
     * @param text $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get comment
     *
     * @return text $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     */
    public function approve()
    {
        if (!$this->approved)
        {
            $this->getPost()->addApprovedComment();
        }
        
        $this->approved = true;
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

        $this->setEmail($user->getEmail());
        $this->setWebSite($user->getWebSite());
        $this->setUsername($user->getUsername());
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
    
}