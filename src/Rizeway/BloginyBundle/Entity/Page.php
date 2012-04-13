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

use Doctrine\ORM\Mapping as ORM;
use Rizeway\BloginyBundle\Model\Utils\StringHandler;

/**
 * Rizeway\BloginyBundle\Entity\Page
 */
class Page
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var boolean $public
     */
    private $public = true;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Rizeway\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var Rizeway\BloginyBundle\Entity\Blog
     */
    private $blogs;

    /**
     * @var Rizeway\UserBundle\Entity\User
     */
    private $users;
    
    /**
     * @var Rizeway\BloginyBundle\Entity\PageHasTag
     */
    private $tags;
    
    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
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
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set public
     *
     * @param boolean $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic()
    {
        return $this->public;
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
     * Set user
     *
     * @param Rizeway\UserBundle\Entity\User $user
     */
    public function setUser(\Rizeway\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Rizeway\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __construct()
    {
        $this->blogs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        
        $this->created_at = new \DateTime();
    }
    
    /*
     * Add blogs
     *
     * @param Rizeway\BloginyBundle\Entity\Blog $blogs
     */
    public function addBlogs(\Rizeway\BloginyBundle\Entity\Blog $blogs)
    {
        $this->blogs[] = $blogs;
    }

    /**
     * Get blogs
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getBlogs()
    {
        return $this->blogs;
    }

    /**
     * Add users
     *
     * @param Rizeway\UserBundle\Entity\User $users
     */
    public function addUsers(\Rizeway\UserBundle\Entity\User $users)
    {
        $this->users[] = $users;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }


    /**
     * Add tags
     *
     * @param Rizeway\BloginyBundle\Entity\PageHasTag $tags
     */
    public function addTags(\Rizeway\BloginyBundle\Entity\PageHasTag $tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * Get tags
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }
    
    /**
     *
     * @param PageHasTag[] $tags 
     */
    public function setTags($tags)
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection($tags);
    } 
    
    /**
     *
     * @param Blog[] $blog 
     */
    public function setBlogs($blogs)
    {
        $this->blogs = new \Doctrine\Common\Collections\ArrayCollection($blogs);
    }
    
    /**
     *
     * @param User[] $user 
     */
    public function setUsers($users)
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection($users);
    }
    
    /**
     * Get a short name for the page
     * @param $length 
     * @return string
     */
    public function getShortName($length = 60)
    {
        $string_handler = new StringHandler();
        return $string_handler->sanitize($string_handler->shorten($this->name, $length));
    }
    
    /**
     * @return string[]
     */
    public function getTagsArray()
    {
        $tags = array();
        foreach ($this->getTags() as $tag) {
            $tags[] = $tag->getTag();
        }
        
        return $tags;
    }
    
    public function isValid()
    {
        return (count($this->getTags()) || count($this->getUsers()) || count($this->getBlogs()));
    }
}