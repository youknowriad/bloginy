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

use Rizeway\BloginyBundle\Model\Utils\StringHandler;

/**
 * Rizeway\BloginyBundle\Entity\Blog
 */
class Blog
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
     * @var string $description
     */
    private $description;

    /**
     * @var string $url
     */
    private $url;

    /**
     * @var string $feed_url
     */
    private $feed_url;

    /**
     * @var string $location
     */
    private $location;

    /**
     * @var string $language
     */
    private $language;

    /**
     * @var boolean $approved
     */
    private $approved = false;

    /**
     * @var integer $votes
     */
    private $votes = 0;

    /**
     * @var integer $rank_value
     */
    private $rank_value = 0;

    /**
     * @var integer $count_views
     */
    private $count_views = 0;

    /**
     * @var datetime $posts_updated_at
     */
    private $posts_updated_at;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var integer $id
     */
    private $id;

    public function  __construct()
    {
        $this->created_at = new \DateTime();
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
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string $url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set feed_url
     *
     * @param string $feedUrl
     */
    public function setFeedUrl($feedUrl)
    {
        $this->feed_url = $feedUrl;
    }

    /**
     * Get feed_url
     *
     * @return string $feedUrl
     */
    public function getFeedUrl()
    {
        return $this->feed_url;
    }

    /**
     * Set location
     *
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * Get location
     *
     * @return string $location
     */
    public function getLocation()
    {
        return $this->location;
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
     * Set votes
     *
     * @param integer $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }

    /**
     * Get votes
     *
     * @return integer $votes
     */
    public function getVotes()
    {
        return $this->votes;
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
     * Set count_views
     *
     * @param integer $countViews
     */
    public function setCountViews($countViews)
    {
        $this->count_views = $countViews;
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
     * Set posts_updated_at
     *
     * @param datetime $postsUpdatedAt
     */
    public function setPostsUpdatedAt($postsUpdatedAt)
    {
        $this->posts_updated_at = $postsUpdatedAt;
    }

    /**
     * Get posts_updated_at
     *
     * @return datetime $postsUpdatedAt
     */
    public function getPostsUpdatedAt()
    {
        return $this->posts_updated_at;
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
     * Calculate the number of stars for this blog ranking value
     * 
     * @param integer $max_stars       Maximum stars for the best blog
     * @param integer $max_rank_value  Maximum rank value for the best blog
     * @return integer
     */
    public function countStars($max_stars, $max_rank_value)
    {
        if ($max_rank_value == 0 || $this->getRankValue() == 0)
        {
            return 0;
        }
        
        return round(($max_stars/$max_rank_value)*($this->getRankValue()));
    }
    
    public function getShortTitle($length = 50)
    {
        $string_handler = new StringHandler();
        return $string_handler->sanitize($string_handler->shorten($this->title, $length));
    }
    
    public function getShortDescription($length = 100)
    {
        $string_handler = new StringHandler();
        return $string_handler->sanitize($string_handler->shorten($this->description, $length));
    }
}