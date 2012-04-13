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
 * Rizeway\BloginyBundle\Entity\Tag
 */
class Tag
{
    /**
     * @var string $tag
     */
    private $tag;

    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var Rizeway\BloginyBundle\Entity\Post
     */
    private $post;

    /**
     * Set tag
     *
     * @param string $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
    }

    /**
     * Get tag
     *
     * @return string $tag
     */
    public function getTag()
    {
        return $this->tag;
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
    
    public function getShortTag($length = 60)
    {
        $string_handler = new StringHandler();
        return $string_handler->sanitize($string_handler->shorten($this->tag, $length));
    }
    
}