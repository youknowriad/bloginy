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

use Rizeway\BloginyBundle\Entity\Category;
use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Model\Utils\Operators;

class PostRepository extends EntityRepository
{
    const SORT_TOP = 'TOP';
    const SORT_DAY = 'DAY';
    const SORT_WEEK = 'WEEK';
    const SORT_MONTH = 'MONTH';
    const SORT_YEAR = 'YEAR';


    public function customFindOneBy($filters)
    {
      $qb = $this->getBaseQueryBuilder();

      foreach($filters as $filter => $value)
      {
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where(\sprintf('post.%s = :%1$s', $filter));
        $qb->setParameter($filter, $value);
      }

      return $qb->getQuery()->getOneOrNullResult();
    }

    /**
    * find the posts from published after $date
    *
    * @param DateTime $date
    * @param integer $max_results
    * @param string $operator // Comparaison operator
    * @return Rizeway\BloginyBundle\Entity\Post[]
    */
    public function findFrom(\DateTime $date, $max_results = null, $operator = Operators::OPERATOR_GREATER_THAN, QueryBuilder $qb = null)
    {
        $qb = $this->getCreatedAtQueryBuilder($date, $operator, $qb);
        $qb->addOrderBy('post.created_at', 'DESC');

        $query = $qb->getQuery();

        if (!is_null($max_results)) {
            $query->setMaxResults($max_results);
        }

        return $query->getResult();
    }

    /**
    * find the top posts
    *
    *
    * @param Category $category
    * @param string $sort
    * @param string $language
    * @param integer $page
    * @param integer $max_results
    * @return Rizeway\BloginyBundle\Entity\Post[]
    */
    public function findTop($category = null, $sort = self::SORT_TOP, $language = 'all', $page = 1, $max_results = null)
    {
        $qb = $this->getTopQueryBuilder($category, $sort, $language);
        
        return $this->findForQueryBuilder($page, $max_results, $qb);
    }
    
    public function findToRank()
    {
        $query = $this->_em->createQueryBuilder()
          ->select('post')
          ->from('BloginyBundle:Post', 'post')
          ->where('post.rank_value < :max')
          ->setParameter('max', 8360)
          ->getQuery();
        
        return $query->getResult();
    }

    /**
     * Get The posts that have the tag
     * 
     * @param string|string[] $tag
     * @param int $page
     * @param int $max_results
     * @return Rizeway\BloginyBundle\Entity\Post[]
     */
    public function findForTag($tag, $page = 1, $max_results = null)
    {
        $qb = $this->getTagQueryBuilder($tag);
        $qb->orderBy('post.rank_value', 'ASC');
        
        return $this->findForQueryBuilder($page, $max_results, $qb);
    }
    
    /**
     * Search posts
     * @param string $filter
     * @param int $page
     * @param int $max_results
     * @return Rizeway\BloginyBundle\Entity\Post[]
     */
    public function search($filter, $page = 1, $max_results = null)
    {
        $qb = $this->getSearchFilterQueryBuilder($filter)
           ->orderBy('post.rank_value', 'ASC');
           
        return $this->findForQueryBuilder($page, $max_results, $qb);
    }
    
    /**
     *
     * @param integer $page
     * @param integer $max_results
     * @param QueryBuilder $qb
     * @return Rizeway\BloginyBundle\Entity\Post[]
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
     * find the posts for a user and published after $date
     *
     * @param User|User[] $user
     * @param integer $page
     * @param integer $max_results
     * @return Rizeway\BloginyBundle\Entity\Post[]
     */
    public function findForUser($user, $page = 1, $max_results = null)
    {
        $qb = $this->getUserQueryBuilder($user);
        $qb->addOrderBy('post.created_at', 'DESC');

        return $this->findForQueryBuilder($page, $max_results, $qb);
    }
    
    /**
     *
     * @param Blog $blog
     * @param type $page
     * @param type $max_results
     * @return Rizeway\BloginyBundle\Entity\Post[] 
     */
    public function findForBlog($blog, $page = 1, $max_results = null)
    {
        $qb = $this->getBlogQueryBuilder($blog);
        $qb->addOrderBy('post.created_at', 'DESC');
        
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
        $qb->$where($qb->expr()->orX('post.title LIKE :filter', 'post.content LIKE :filter'))
           ->setParameter('filter', '%'.$filter.'%');
        
        return $qb;
    }

    /**
     * Get The query builder for the top page
     *
     * @param Category $category
     * @param string $sort
     * @param string $language
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function getTopQueryBuilder($category = null, $sort = self::SORT_TOP, $language = 'all', QueryBuilder $qb = null)
    {
        $qb = $this->getCategoryQueryBuilder($category, $qb);
        $qb = $this->getSortQueryBuilder($sort, $qb);
        $qb = $this->getLanguageQueryBuilder($language, $qb);

        return $qb;
    }

    /**
     * get query builder with category filter
     *
     * @param Category $category
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function getCategoryQueryBuilder($category = null, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        if (!is_null($category)) {
            $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
            $qb->innerJoin('post.category', 'category', 'WITH', 'category.name = :category_name');
            $qb->setParameter('category_name', $category->getName());
        }

        return $qb;
    }

    /**
     * Get the queryBuilder by adding the user filter
     * 
     * @param User $user
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getUserQueryBuilder($user, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;

        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        
        if ($user instanceof User) {
           $qb->$where('post.user = :user')
             ->setParameter('user', $user->getId()); 
        } else {
           $ids = array();
           foreach ($user as $u) {
               $ids[] = $u->getId();
           }
           $qb->$where('post.user IN (:user)')
             ->setParameter('user', $ids); 
        }

        return $qb;
    }

    /**
     * Add the tag filter to the query builder
     * @param string|string[] $tag
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getTagQueryBuilder($tag, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        if (\is_array($tag)) {
            $qb->join('post.tags', 'tags', Expr\Join::WITH, 'tags.tag IN (:tag)');
        } else {
            $qb->join('post.tags', 'tags', Expr\Join::WITH, 'tags.tag = :tag');
        }

        $qb->setParameter('tag', $tag);

        return $qb;
    }

    /**
     * get query builder with sorting type
     *
     * @param string $sort
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function getSortQueryBuilder($sort = self::SORT_TOP, QueryBuilder $qb = null)
    {
        switch ($sort) {
            case self::SORT_TOP :
                return $this->getSortTopQueryBuilder($qb);
            case self::SORT_DAY :
                return $this->getSortDayQueryBuilder($qb);
            case self::SORT_WEEK :
                return $this->getSortWeekQueryBuilder($qb);
            case self::SORT_MONTH :
                return $this->getSortMonthQueryBuilder($qb);
            case self::SORT_YEAR :
                return $this->getSortYearQueryBuilder($qb);
            default:
                throw new \Exception('Sorting Type Unknown');
        }
    }

    /**
    * Get the query builder for the top posts
    *
    * @param QueryBuilder $qb
    * @return QueryBuilder
    */
    protected function getSortTopQueryBuilder(QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $qb->orderBy('post.rank_value', 'ASC');

        return $qb;
    }

    /**
    * Get the query builder for the top posts of the day
    *
    * @param QueryBuilder $qb
    * @return QueryBuilder
    */
    protected function getSortDayQueryBuilder(QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $qb->orderBy('post.count_votes', 'DESC');

        $limit = new \DateTime();
        $limit->setTime(0, 0, 0);
        $inf = $limit->format('Y-m-d H:i:sP');
        $limit->setTime(23, 59, 59);
        $sup = $limit->format('Y-m-d H:i:sP');
        $date = new \DateTime();

        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('post.created_at < :created_at_sup_limit');
        $qb->andWhere('post.created_at > :created_at_inf_limit');
        $qb->setParameter('created_at_sup_limit', $sup);
        $qb->setParameter('created_at_inf_limit', $inf);

        return $qb;
    }

    /**
    * Get the query builder for the top posts of the last 7 days
    *
    * @param QueryBuilder $qb
    * @return QueryBuilder
    */
    protected function getSortWeekQueryBuilder(QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $qb->orderBy('post.count_votes', 'DESC');

        $limit = new \DateTime();
        $limit->setTime(23, 59, 59);
        $sup = $limit->format('Y-m-d H:i:sP');
        $limit->setTime(0, 0, 0);
        $limit->modify('-7 Days');
        $inf = $limit->format('Y-m-d H:i:sP');

        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('post.created_at < :created_at_sup_limit');
        $qb->andWhere('post.created_at > :created_at_inf_limit');
        $qb->setParameter('created_at_sup_limit', $sup);
        $qb->setParameter('created_at_inf_limit', $inf);

        return $qb;
    }

    /**
    * Get the query builder for the top posts of the last 30 days
    *
    * @param QueryBuilder $qb
    * @return QueryBuilder
    */
    protected function getSortMonthQueryBuilder(QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $qb->orderBy('post.count_votes', 'DESC');

        $limit = new \DateTime();
        $limit->setTime(23, 59, 59);
        $sup = $limit->format('Y-m-d H:i:sP');
        $limit->setTime(0, 0, 0);
        $limit->modify('-30 Days');
        $inf = $limit->format('Y-m-d H:i:sP');

        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('post.created_at < :created_at_sup_limit');
        $qb->andWhere('post.created_at > :created_at_inf_limit');
        $qb->setParameter('created_at_sup_limit', $sup);
        $qb->setParameter('created_at_inf_limit', $inf);

        return $qb;
    }

    /**
    * Get the query builder for the top posts of the year
    *
    * @param QueryBuilder $qb
    * @return QueryBuilder
    */
    protected function getSortYearQueryBuilder(QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $qb->orderBy('post.count_votes', 'DESC');

        $limit = new \DateTime();
        $limit->setTime(0, 0, 0);
        $limit->setDate(date('Y'), 1, 1);
        $inf = $limit->format('Y-m-d H:i:sP');
        $limit->setTime(0, 0, 0);
        $limit->modify('+1 Year');
        $sup = $limit->format('Y-m-d H:i:sP');

        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('post.created_at < :created_at_sup_limit');
        $qb->andWhere('post.created_at > :created_at_inf_limit');
        $qb->setParameter('created_at_sup_limit', $sup);
        $qb->setParameter('created_at_inf_limit', $inf);

        return $qb;
    }

    /**
     * Get the query builder for the language filter
     *
     * @param string $language
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function getLanguageQueryBuilder($language = 'all', QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        if ($language != 'all')
        {
            $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
            $qb->$where('post.language = :language');
            $qb->setParameter('language', $language);
        }

        return $qb;
    }


    /**
     * Get the query builder for the publication filter
     *
     * @param \DateTime $date // publication date
     * @param string $operator // Comparaison operator
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getCreatedAtQueryBuilder(\DateTime $date, $operator = Operators::OPERATOR_GREATER_THAN , QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where(sprintf('(post.created_at %s :created_at)', $operator));
        $qb->setParameter('created_at', $date->format('Y-m-d H:i:sP'));

        return $qb;
    }
    
    /**
     *
     * @param Blog $blog
     * @return QueryBuilder 
     */
    protected function getBlogQueryBuilder($blog)
    {
        $qb = $this->_em->createQueryBuilder()
          ->select('post, user, blog_post')
          ->from('BloginyBundle:Post', 'post')
          ->join('post.user', 'user')
          ->join('post.blog_post', 'blog_post')
          ->where('blog_post.blog = :blog')
          ->setParameter('blog', $blog->getId());
        
        return $qb;
    }

    /**
    * Get the base query builder
    * @return QueryBuilder
    */
    protected function getBaseQueryBuilder()
    {
        $qb = $this->_em->createQueryBuilder()
          ->select('post, user, blog_post')
          ->from('BloginyBundle:Post', 'post')
          ->join('post.user', 'user')
          ->leftJoin('post.blog_post', 'blog_post');
        
        return $qb;
    }
}