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

namespace Rizeway\BloginyBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use \Rizeway\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {    
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword('admin');
        $user->setEmail('contact@bloginy.com');
        $user->setApproved(true);
        $user->setLocale('fr');

        $manager->persist($user);

        $user2 = new User();
        $user2->setUsername('youknowriad');
        $user2->setPassword('Riad');
        $user2->setEmail('benguella@gmail.com');
        $user2->setApproved(true);
        $user2->setLocale('fr');

        $manager->persist($user2);
        $manager->flush();

        $this->addReference('admin-user', $user);
        $this->addReference('simple-user', $user2);
    }

    public function getOrder()
    {
        return 1;
    }
}