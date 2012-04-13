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

namespace Rizeway\UserBundle\Model\Repository;

use Doctrine\ORM\EntityRepository;

class UserActivationRepository extends EntityRepository
{
    
    /**
     *
     * @param Rizeway\UserBundle\Entity\User $user
     */
    public function findOneByUser($user)
    {
        $qb = $this->getUserQueryBuilder($user);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch(\Doctrine\ORM\NoResultException $e) {
            return null;
        }   
    }

    /**
     * get query builder with user filter
     *
     * @param \Rizeway\UserBundleBundle\Entity\User $user
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getUserQueryBuilder($user, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        $qb->$where('activation.user = :user_id');
        $qb->setParameter('user_id', $user->getId());

        return $qb;
    }

    /**
    * Get the base query builder
    * @return QueryBuilder
    */
    public function getBaseQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
          ->select('activation')
          ->from('UserBundle:UserActivation', 'activation');
    }
}