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
 * Rizeway\BloginyBundle\Entity\Visit
 */
class Visit
{
    /**
     * @var string $ip_adress
     */
    private $ip_adress;

    /**
     * @var datetime $created_at
     */
    private $created_at;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Rizeway\BloginyBundle\Entity\Post
     */
    private $post;

    public function  __construct()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * Set ip_adress
     *
     * @param string $ipAdress
     */
    public function setIpAdress($ipAdress)
    {
        $this->ip_adress = $ipAdress;
    }

    /**
     * Get ip_adress
     *
     * @return string $ipAdress
     */
    public function getIpAdress()
    {
        return $this->ip_adress;
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