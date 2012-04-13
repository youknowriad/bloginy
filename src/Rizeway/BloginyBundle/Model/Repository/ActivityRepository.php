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
use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Entity\Comment;

class ActivityRepository extends EntityRepository
{
    /**
    * find the posts from published after $date
    *
    * @param DateTime $date
    * @param integer $max_results
    * @param string $operator // Comparaison operator
    * @return Rizeway\BloginyBundle\Entity\Activity[]
    */
    public function findFrom(\DateTime $date, $max_results = null, $operator = Operators::OPERATOR_GREATER_THAN, QueryBuilder $qb = null)
    {
        $qb = $this->getCreatedAtQueryBuilder($date, $operator, $qb);
        $qb->addOrderBy('activity.created_at', 'DESC');

        $query = $qb->getQuery();

        if (!is_null($max_results)) {
            $query->setMaxResults($max_results);
        }

        return $query->getResult();
    }
    
    /**
    * find the posts from published after $date
    *
    * @param User $user
    * @param DateTime $date
    * @param integer $max_results
    * @param string $operator // Comparaison operator
    * @return Rizeway\BloginyBundle\Entity\Activity[]
    */
    public function findForUser(User $user, \DateTime $date, $max_results = null, $operator = Operators::OPERATOR_LESS_THAN, QueryBuilder $qb = null)
    {
        $qb = $this->getCreatedAtQueryBuilder($date, $operator, $qb);
        $qb = $this->getUserQueryBuilder($user, $qb);
        $qb->addOrderBy('activity.created_at', 'DESC');

        $query = $qb->getQuery();
        if (!is_null($max_results)) {
            $query->setMaxResults($max_results);
        }

        return $query->getResult();
    }
    
    /**
     * Get the query builder for the publication filter
     *
     * @param \DateTime $date // publication date
     * @param string $operator // Comparaison operator
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    protected function getCreatedAtQueryBuilder(\DateTime $date, $operator = Operators::OPERATOR_GREATER_THAN , QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where(sprintf('(activity.created_at %s :created_at)', $operator));
        $qb->setParameter('created_at', $date->format('Y-m-d H:i:sP'));

        return $qb;
    }
    
    /**
     * Get the query builder for the user filter
     *
     * @param User $user
     * @return QueryBuilder
     */
    protected function getUserQueryBuilder(USer $user, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('(activity.user = :user)');
        $qb->andWhere('activity.user IS NOT NULL');
        $qb->setParameter('user', $user->getId());

        return $qb;
    }

    /**
    * Get the base query builder
    * @return QueryBuilder
    */
    protected function getBaseQueryBuilder()
    {
        $qb = $this->_em->createQueryBuilder()
          ->select('activity, post, page, user, comment, blog')
          ->from('BloginyBundle:Activity', 'activity')
          ->leftJoin('activity.post', 'post')
          ->leftJoin('activity.page', 'page')
          ->leftJoin('activity.blog', 'blog')
          ->leftJoin('activity.user', 'user')
          ->leftJoin('activity.comment', 'comment');
        
        return $qb;
    }
    
    public function removeForComment(Comment $comment)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->delete()
           ->from('BloginyBundle:Activity', 'activity')
           ->where('activity.comment = :comment')
           ->setParameter('comment', $comment->getId());
        
        $qb->getQuery()->getResult();
    }
}