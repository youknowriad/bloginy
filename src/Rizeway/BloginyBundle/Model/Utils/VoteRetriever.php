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

namespace Rizeway\BloginyBundle\Model\Utils;

use Doctrine\ORM\EntityManager;
use Rizeway\BloginyBundle\Entity\Post;
use Rizeway\BloginyBundle\Entity\BlogPost;
use Rizeway\UserBundle\Entity\User;

class VoteRetriever
{

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * Get Votes for posts and User
     * 
     * @param User $user
     * @param Post[] $posts
     */
    public function getVotesForPosts(User $user, $posts)
    {
        $ids = \array_map(function($v) { return $v->getId(); }, $posts);
        $votes = $this->em->getRepository('BloginyBundle:Vote')
            ->findByUserAndPosts($user, $ids);

        if (\count($votes))
        {
            $votes = \array_combine(\array_map(function($v) { return $v->getPost()->getId();}, $votes), \array_map(function($v) { return true; }, $votes));
        }

        return $votes;
    }

    /**
     * Get Votes for blog posts and User
     *
     * @param User $user
     * @param BlogPost[] $posts
     */
    public function getVotesForBlogPosts(User $user, $posts)
    {
        $ids = \array_map(function($v) { return $v->getId(); }, $posts);
        $votes = $this->em->getRepository('BloginyBundle:Vote')
            ->findByUserAndBlogPosts($user, $ids);

        if (\count($votes))
        {
            $votes = \array_combine(\array_map(function($v) { return $v->getPost()->getBlogPost()->getId();}, $votes), \array_map(function($v) { return true; }, $votes));
        }

        return $votes;
    }
}