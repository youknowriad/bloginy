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

use Rizeway\BloginyBundle\Model\Utils\SlugGenerator;
use Rizeway\BloginyBundle\Entity\ApiLog;
use Doctrine\ORM\EntityManager;

class ApiAuthenticator
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
    
    public function login($code, $client, $function)
    {
        $handler = new SlugGenerator($this->entity_manager->getRepository('UserBundle:User'));
        $client = $handler->slugify($client);

        if ($client == "" || $code == "") {
            return false;
        }

        // Checking User
        $user = $this->entity_manager->getRepository('UserBundle:User')->findOneBy(array('api_code' => $code));

        if (\is_null($user)) {
            return false;
        }
        
        // Log Creation
        $log = new ApiLog();
        $log->setUser($user);
        $log->setClient($client);
        $log->setFunction($function);

        $this->entity_manager->persist($log);
        $this->entity_manager->flush();

        return true;
    }
}