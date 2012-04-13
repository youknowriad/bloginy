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

namespace Rizeway\BloginyBundle\Form\DataTransform;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManager;

class UsersTransform implements DataTransformerInterface
{
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $usernames = \explode(',', $value);
        $users = $this->em->getRepository('UserBundle:User')->findByUsername($usernames);
        
        return $users;
    }
    
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (\is_null($value))
        {
            return '[]';
        }
        
        $usernames = array();
        foreach ($value as $user)
        {
            $usernames[] = array('id' => $user->getUsername(), 'label' => $user->getUsername());
        }
        
        return json_encode($usernames);
    }
}