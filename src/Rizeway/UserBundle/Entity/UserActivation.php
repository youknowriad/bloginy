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

namespace Rizeway\UserBundle\Entity;

use Rizeway\UserBundle\Lib\StringUtils;

/**
 * Rizeway\UserBundle\Entity\UserActivation
 */
class UserActivation
{
    /**
     * @var string $code
     */
    private $code;

    /**
     * @var integer $count_tries
     */
    private $count_tries = 0;


    /**
     * @var boolean $approved
     */
    private $locked = false;

    /**
     * @var datetime $created_at
     */
    private $created_at;
    
    /**
     * @var Rizeway\UserBundle\Entity\User
     */
    private $user;

    /**
     * @var integer $id
     */
    private $id;

    public function  __construct()
    {
        $this->created_at = new \DateTime();
        $this->code = StringUtils::generateRandomString(32);
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string $code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set count tries
     *
     * @param string $count
     */
    public function setCountTries($count)
    {
        $this->count_tries = $count;
    }

    /**
     * Get count tries
     *
     * @return string $count_tries
     */
    public function getCountTries()
    {
        return $this->count_tries;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;
    }

    /**
     * Get locked
     *
     * @return boolean $locked
     */
    public function getLocked()
    {
        return $this->locked;
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
     * @param Rizeway\UserBundle\Entity\User $user
     */
    public function setUser(\Rizeway\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Rizeway\UserBundle\Entity\User $user
     */
    public function getUser()
    {
        return $this->user;
    }
    
}