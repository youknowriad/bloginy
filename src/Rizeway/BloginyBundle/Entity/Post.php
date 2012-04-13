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

use Doctrine\Common\Collections\ArrayCollection;
use Rizeway\BloginyBundle\Model\Utils\StringHandler;

/**
 * Rizeway\BloginyBundle\Entity\Post
 */
class Post
{
    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var text $content
     */
    private $content;

    /**
     * @var string $link
     */
    private $link;

    /**
     * @var string $language
     */
    private $language;

    /**
     * @var integer $count_votes
     */
    private $count_votes = 0;
    
    /**
     * @var integer $count_comments
     */
    private $count_comments = 0;

    /**
     * @var integer $rank_value
     */
    private $rank_value = 0;

    /**
     * @var integer $count_views
     */
    private $count_views = 0;

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
     * @var Rizeway\BloginyBundle\Entity\BlogPost
     */
    private $blog_post;

    /**
     * @var Rizeway\BloginyBundle\Entity\Comment
     */
    private $comments;

    /**
     * @var Rizeway\BloginyBundle\Entity\Tag
     */
    private $tags;

    /**
     * @var Rizeway\BloginyBundle\Entity\Vote
     */
    private $votes;
    
    /**
     * @var Rizeway\BloginyBundle\Entity\Visit
     */
    private $visits;
    
    /**
     * @var Rizeway\BloginyBundle\Entity\Activity
     */
    private $activities;


    /**
     * @var \Rizeway\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var Rizeway\BloginyBundle\Entity\Category
     */
    private $category;
    
    public function  __construct()
    {
        $this->created_at = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
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
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text $content
     */
    public function getContent()
    {
        return $this->content;
    }

    public function getResume()
    {
        $string_handler = new StringHandler();
        return $string_handler->sanitize($string_handler->shorten($this->content, 300));
    }

    /**
     * Set link
     *
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Get link
     *
     * @return string $link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get language
     *
     * @return string $language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get count_votes
     *
     * @return integer $countVotes
     */
    public function getCountVotes()
    {
        return $this->count_votes;
    }

    /**
     * Set rank_value
     *
     * @param integer $rankValue
     */
    public function setRankValue($rankValue)
    {
        $this->rank_value = $rankValue;
    }

    /**
     * Get rank_value
     *
     * @return integer $rankValue
     */
    public function getRankValue()
    {
        return $this->rank_value;
    }

    /**
     * Get count_views
     *
     * @return integer $countViews
     */
    public function getCountViews()
    {
        return $this->count_views;
    }
    
    /**
     * Get count_comments
     *
     * @return integer $count_comments
     */
    public function getCountComments()
    {
        return $this->count_comments;
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
     * Set blog_post
     *
     * @param Rizeway\BloginyBundle\Entity\BlogPost $blogPost
     */
    public function setBlogPost(\Rizeway\BloginyBundle\Entity\BlogPost $blogPost)
    {
        $this->blog_post = $blogPost;
    }

    /**
     * Get blog_post
     *
     * @return Rizeway\BloginyBundle\Entity\BlogPost $blogPost
     */
    public function getBlogPost()
    {
        return $this->blog_post;
    }

    /**
     * Add comments
     *
     * @param Rizeway\BloginyBundle\Entity\Comment $comments
     */
    public function addComments(\Rizeway\BloginyBundle\Entity\Comment $comments)
    {
        if ($comments->getApproved())
        {
            $this->count_comments++;
        }
        
        $this->comments[] = $comments;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add tags
     *
     * @param Rizeway\BloginyBundle\Entity\Tag $tags
     */
    public function addTags(\Rizeway\BloginyBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;
    }

    /**
     * Get tags
     *
     * @return Doctrine\Common\Collections\Collection $tags
     */
    public function getTags()
    {
        return $this->tags;
    }
    
    /**
     *
     * @param Rizeway\BloginyBundle\Entity\Tag[] $tags 
     */
    public function setTags($tags)
    {
        $this->tags = new ArrayCollection($tags);
    }

    /**
     * Add votes
     *
     * @param Rizeway\BloginyBundle\Entity\Vote $votes
     */
    public function addVotes(\Rizeway\BloginyBundle\Entity\Vote $votes)
    {
        $this->count_votes++;
        $this->votes[] = $votes;
    }

    /**
     * Get $visits
     *
     * @return Doctrine\Common\Collections\Collection $visits
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Add $visits
     *
     * @param Rizeway\BloginyBundle\Entity\Visit $visits
     */
    public function addVisits(\Rizeway\BloginyBundle\Entity\Visit $visits)
    {
        $this->visits[] = $visits;
    }

    /**
     * Get votes
     *
     * @return Doctrine\Common\Collections\Collection $votes
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Get activities
     *
     * @return Doctrine\Common\Collections\Collection $activities
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Add activities
     *
     * @param Rizeway\BloginyBundle\Entity\Activity $activities
     */
    public function addActivities(\Rizeway\BloginyBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;
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
     * Set category
     *
     * @param Rizeway\BloginyBundle\Entity\Category $category
     */
    public function setCategory(\Rizeway\BloginyBundle\Entity\Category $category)
    {
        $this->category = $category;
    }

    /**
     * Get category
     *
     * @return Rizeway\BloginyBundle\Entity\Category $category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get the value used for the blog ranking algorithm
     */
    public function getBlogRankingValue()
    {
        return $this->getCountVotes() * 5 + $this->getCountViews();
    }
    
    public function getShortTitle($length = 60)
    {
        $string_handler = new StringHandler();
        return $string_handler->sanitize($string_handler->shorten($this->title, $length));
    }
    
    /**
     * This method should never be called except by the Comment approve method
     */
    public function addApprovedComment()
    {
        $this->count_comments++;
    }
    
    public function deleteApprovedComment()
    {
        $this->count_comments--;
    }
    
    public function incrementCountViews() 
    {
        $this->count_views++;
    }
}