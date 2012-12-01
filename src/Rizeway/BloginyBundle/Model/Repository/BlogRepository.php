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
use Doctrine\ORM\Query\Expr;
use Rizeway\BloginyBundle\Model\Utils\Operators;
use Doctrine\ORM\Query\ResultSetMapping;

class BlogRepository extends EntityRepository
{
    /**
     * Find for a query builder with possible pagination
     *
     * @param integer $page
     * @param integer $max_results
     * @param QueryBuilder $qb
     * @return Rizeway\BloginyBundle\Entity\Blog[]
     */
    public function findForQueryBuilder($page = 1, $max_results = null, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;

        $query = $qb->getQuery();
        if (!is_null($max_results)) {
            $query->setMaxResults($max_results);
            $query->setFirstResult(($page - 1) * $max_results);
        }

        return $query->getResult();
    }

    /**
     * find one by
     * 
     * @param str[] $filters
     * @return NULL|Blog
     */
    public function customFindOneBy($filters)
    {
      $qb = $this->getBaseQueryBuilder();

      foreach($filters as $filter => $value)
      {
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where(\sprintf('blog.%s = :%1$s', $filter));
        $qb->setParameter($filter, $value);
      }

      return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Find the top blogs
     * @param string $location
     * @param string $language
     * @param integer $page
     * @param integer $max_results
     * @param QueryBuilder $qb
     * @return Rizeway\BloginyBundle\Entity\Blog[]
     */
    public function findTop($location = 'all', $language = 'all', $page = 1, $max_results = null, QueryBuilder $qb = null)
    {
        $qb = $this->getApprovedQueryBuilder(1, $qb);
        $qb = $this->getLocationQueryBuilder($location, $qb);
        $qb = $this->getLanguageQueryBuilder($language, $qb);
        $qb->addOrderBy('blog.created_at', 'DESC');
        
        return $this->findForQueryBuilder($page, $max_results, $qb);
    }

    /**
     * find the blogs ready to update
     * @param \DateTime $date // Date last update
     * @param QueryBuilder $qb
     * @return Rizeway\BloginyBundle\Entity\Blog[]
     */
    public function findReadyToUpdate(\DateTime $date, QueryBuilder $qb = null)
    {
        $qb = $this->getApprovedQueryBuilder(1, $qb);
        $qb = $this->getPostsUpdatedAtQueryBuilder($date, Operators::OPERATOR_LESS_THAN, $qb);

        return $qb->getQuery()->getResult();
    }
    
    /**
     * Find the daily blog
     * @return Rizeway\BloginyBundle\Entity\Blog
     */
    public function findDailyBlog()
    {   
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('BloginyBundle:Blog', 'blog');
        $rsm->addFieldResult('blog', 'id', 'id');
        $rsm->addFieldResult('blog', 'slug', 'slug');
        $rsm->addFieldResult('blog', 'title', 'title');
        $rsm->addFieldResult('blog', 'description', 'description');
        $query = $this->_em->createNativeQuery('SELECT Blog.id, Blog.slug, Blog.title, Blog.description FROM Blog JOIN DailyBlog ON (Blog.id = DailyBlog.blog_id) WHERE DailyBlog.active = ? LIMIT 1', $rsm);
        $query->setParameter(1, 1);

        return current($query->getResult());
    }
    
    /**
     * Find Blogs for slugs
     * @param string|string[] $slugs
     * @param QueryBuilder $qb
     * @return Rizeway\BloginyBundle\Entity\Blog[] 
     */
    public function findBySlugs($slugs, QueryBuilder $qb = null)
    {
        $qb = $this->getSlugQueryBuilder($slugs, $qb);
        
        return $this->findForQueryBuilder(1, null, $qb);
    }
    
    public function findLast($max_results)
    {
        $qb = $this->getBaseQueryBuilder();
        $qb->orderBy('blog.created_at', 'DESC');
        
        return $this->findForQueryBuilder(1, $max_results, $qb);
    }
    
    /**
     *
     * @param string $title
     * @param integer $limit
     * @return Rizeway\BloginyBundle\Entity\Blog[]  
     */
    public function filterByTitle($title, $limit)
    {
        $query = $this->getBaseQueryBuilder()
            ->where('blog.title LIKE :filter')
            ->setParameter('filter', '%'.$title.'%')
            ->getQuery()
            ->setMaxResults($limit);
        
        return $query->getResult();
    }
    
    /**
     * Search blogs
     * @param string $filter
     * @param int $page
     * @param int $max_results
     * @return Rizeway\BloginyBundle\EntityBlog[]
     */
    public function search($filter, $page = 1, $max_results = null)
    {
        $qb = $this->getSearchFilterQueryBuilder($filter);
        $qb = $this->getApprovedQueryBuilder(1, $qb)
           ->orderBy('blog.created_at', 'DESC');
           
        return $this->findForQueryBuilder($page, $max_results, $qb);
    }
    
    /**
     * Get the search query builder
     * @param string $filter
     * @param QueryBuilder $qb
     * @return Query 
     */
    public function getSearchFilterQueryBuilder($filter, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where($qb->expr()->orX('blog.title LIKE :filter', 'blog.description LIKE :filter'))
           ->setParameter('filter', '%'.$filter.'%');
        
        return $qb;
    }
    
    /**
     * Get the query builder for the slug filter
     * @param string|string[] $slugs
     * @param QueryBuilder $qb
     * @return QueryBuilder 
     */
    protected function getSlugQueryBuilder($slugs, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        if (\is_array($slugs)) {
            $qb->$where('blog.slug IN (:slug)');
        } else {
            $qb->$where('blog.slug = :slug');
        }
        $qb->setParameter('slug', $slugs);
        
        return $qb;
    }

    /**
     * Get the query builder for approved filter
     *
     * @param bool $approved
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getApprovedQueryBuilder($approved = 1, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('blog.approved = :approved');
        $qb->setParameter('approved', $approved);
        
        return $qb;
    }

    /**
     * Get the query builder for the updatedAt filter
     * 
     * @param \DateTime $date // update date
     * @param string $operator // Comparaison operator
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getPostsUpdatedAtQueryBuilder(\DateTime $date, $operator = Operators::OPERATOR_GREATER_THAN , QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where(sprintf('(blog.posts_updated_at IS NULL or blog.posts_updated_at %s :posts_updated_at)', $operator));
        $qb->setParameter('posts_updated_at', $date->format('Y-m-d H:i:sP'));

        return $qb;
    }

    /**
     * Get the query builder for the location filter
     *
     * @param string $location
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getLocationQueryBuilder($location = 'all', QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        if ($location != 'all')
        {
            $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
            $qb->$where('blog.location = :location');
            $qb->setParameter('location', $location);
        }

        return $qb;
    }

    /**
     * Get the query builder for the language filter
     *
     * @param string $language
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getLanguageQueryBuilder($language = 'all', QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        if ($language != 'all')
        {
            $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
            $qb->$where('blog.language = :language');
            $qb->setParameter('language', $language);
        }

        return $qb;
    }

    /**
    * Get the base query builder
     * 
    * @return QueryBuilder
    */
    public function getBaseQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
          ->select('blog')
          ->from('BloginyBundle:Blog', 'blog');
    }
}