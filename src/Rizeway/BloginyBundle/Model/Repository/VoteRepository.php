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

namespace Rizeway\BloginyBundle\Model\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Entity\Post;
use Rizeway\BloginyBundle\Entity\BlogPost;

class VoteRepository extends EntityRepository
{

    /**
     * Find by user and posts ids
     *
     * @param User $user
     * @param $post_ids
     * @return Rizeway\BloginyBundle\Entity\Vote[]
     */
    public function findByUserAndPosts(User $user, $post_ids)
    {
        if (!count($post_ids))
        {
            return array();
        }

        $qb = $this->getUserQueryBuilder($user);
        $qb = $this->getPostsQueryBuilder($post_ids, $qb);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find by user and blog posts ids
     *
     * @param User $user
     * @param $post_ids
     * @return Rizeway\BloginyBundle\Entity\Vote[]
     */
    public function findByUserAndBlogPosts(User $user, $post_ids)
    {
        if (!count($post_ids))
        {
            return array();
        }
        
        $qb = $this->getUserQueryBuilder($user);
        $qb = $this->getBlogPostsQueryBuilder($post_ids, $qb);

        return $qb->getQuery()->getResult();
    }
    


    /**
     * Find by user and post
     * 
     * @param User $user
     * @param Post $post
     * @return Rizeway\BloginyBundle\Entity\Vote
     */
    public function findByUserAndPost(User $user, Post $post)
    {
        $qb = $this->getUserQueryBuilder($user);
        $qb = $this->getPostQueryBuilder($post, $qb);

        try
        {
           return $qb->getQuery()->getSingleResult();
        }
        catch(\Exception $e)
        {
            return null;
        }
    }

    /**
     * Find by user and post
     *
     * @param User $user
     * @param Post $post
     * @return Rizeway\BloginyBundle\Entity\Vote
     */
    /**
     * @param \Rizeway\UserBundle\Entity\User $user
     * @param \Rizeway\BloginyBundle\Entity\BlogPost $post
     * @return Rizeway\BloginyBundle\Entity\Vote|null
     */
    public function findByUserAndBlogPost(User $user, BlogPost $post)
    {
        $qb = $this->getUserQueryBuilder($user);
        $qb = $this->getBlogPostsQueryBuilder(array($post->getId()), $qb);

        try
        {
            return $qb->getQuery()->getSingleResult();
        }
        catch(\Exception $e)
        {
            return null;
        }
    }


    /**
     * Get the query builder by post
     * 
     * @param Post $post
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function getPostQueryBuilder(Post $post, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;

        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('vote.post = :post');
        $qb->setParameter('post', $post->getId());

        return $qb;
    }

    /**
     * Get the query builder by blog posts ids
     *
     * @param int[] $post_ids
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function getBlogPostsQueryBuilder($post_ids, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;

        $qb->join('vote.post', 'post')
           ->join('post.blog_post', 'blog_post');
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where($qb->expr()->in('blog_post.id', $post_ids));

        return $qb;
    }

    /**
     * Get the query builder by posts
     *
     * @param int[] $post_ids
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function getPostsQueryBuilder($post_ids, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;

        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where($qb->expr()->in('vote.post', $post_ids));

        return $qb;
    }

    /**
     * GEt the query builder for user
     *
     * @param User $user
     * @param QueryBuilder $qb
     * @return <type>
     */
    protected function getUserQueryBuilder(User $user, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;

        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('vote.user = :user');
        $qb->setParameter('user', $user->getId());

        return $qb;
    }

    /**
    * Get the base query builder
    * @return QueryBuilder
    */
    public function getBaseQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
          ->select('vote')
          ->from('BloginyBundle:Vote', 'vote');
    }
}