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

use Rizeway\BloginyBundle\Entity\Visit;
use Rizeway\BloginyBundle\Entity\Post;
use Doctrine\ORM\EntityManager;

class PostVisitHandler
{
    
    /**
     * Doctrine Entity Manager
     * 
     * @var EntityManager
     */
    private $entity_manager;
    
    public function __construct(EntityManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }
    
    public function checkVisit(Post $post)
    {
        $ipAdress = $this->getIpAdress();
        $visit = $this->entity_manager->getRepository('BloginyBundle:Visit')->findOneBy(array('post' => $post->getId(), 'ip_adress' => $ipAdress));

        if (\is_null($visit))
        {
            $visit = new Visit();
            $visit->setPost($post);
            $visit->setIpAdress($ipAdress);
            $this->entity_manager->persist($visit);
            $post->incrementCountViews();
            $this->entity_manager->flush();
        }
    }
    
    protected function getIpAdress()
    {
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif(isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
        
		return $ip;
    }
}
