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
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    /**
     * methode used by security provider to retrieve the user
     * @param string $username (or email)
     * @return Rizeway\UserBundle\Entity\User $user
     */
    public function loadUserByUsername($username)
    {
        $qb = $this->getUsernameQueryBuilder($username);

        return $qb->getQuery()->getSingleResult();
    }
    
    /**
     *
     * @param string[] $usernames 
     * @return Rizeway\UserBundle\Entity\User[]
     */
    public function findByUsernames($usernames)
    {
        $qb = $this->getUsernameQueryBuilder($username);

        return $qb->getQuery()->getResult();
    }
    
    /**
     *
     * @param string $username
     * @param integer $limit
     * @return Rizeway\UserBundle\Entity\User[]
     */
    public function filterByUsername($username, $limit)
    {
        $query = $this->getBaseQueryBuilder()
            ->where('user.username LIKE :filter')
            ->setParameter('filter', '%'.$username.'%')
            ->getQuery()
            ->setMaxResults($limit);
        
        return $query->getResult();
    }
    
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    function supportsClass($class)
    {
        if ($class == 'Rizeway\UserBundle\Entity\User')
        {
            return true;
        }

        return false;
    }

    /**
     * get query builder with username filter
     *
     * @param @param string|string[] $username
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function getUsernameQueryBuilder($username, QueryBuilder $qb = null)
    {
        $qb = is_null($qb) ? $this->getBaseQueryBuilder() : $qb;
        $where = is_null($qb->getDQLPart('where')) ? 'where' : 'andWhere';
        
        if (is_array($username)) {
            $qb->$where('user.username IN (:login)');
        } else {
            $qb->$where($qb->expr()->orX('user.email = :login', 'user.username = :login'));
        }
        $qb->setParameter('login', $username);

        return $qb;
    }

    /**
    * Get the base query builder
    * @return QueryBuilder
    */
    public function getBaseQueryBuilder()
    {
        return $this->_em->createQueryBuilder()
          ->select('user')
          ->from('UserBundle:User', 'user');
    }
}