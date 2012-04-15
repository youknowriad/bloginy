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
use Rizeway\BloginyBundle\Entity\Blog;
use Doctrine\Common\Persistence\ObjectManager;

class BlogData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {    
        $blog = new Blog();
        $blog->setTitle('YouKnowRiad');
        $blog->setSlug('youknowriad');
        $blog->setDescription('Blog de Riad Benguella');
        $blog->setUrl('http://blog.riadbenguella.com');
        $blog->setFeedUrl('http://blog.riadbenguella.com/feed/');
        $blog->setApproved(true);
        $blog->setLocation('Oran');
        $blog->setLanguage('fr');

        $manager->persist($blog);
        $manager->flush();

        $this->addReference('youknowriad-blog', $blog);
    }

    public function getOrder()
    {
        return 3;
    }
}