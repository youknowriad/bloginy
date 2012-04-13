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
use Rizeway\BloginyBundle\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Economy');
        $category->setDescription('');
        $manager->persist($category);

        $category = new Category();
        $category->setName('Politics');
        $category->setDescription('');
        $manager->persist($category);
        
        $category_tech = new Category();
        $category_tech->setName('Technology');
        $category_tech->setDescription('');
        $manager->persist($category_tech);

        $category = new Category();
        $category->setName('Health');
        $category->setDescription('');
        $manager->persist($category);

        $category = new Category();
        $category->setName('Women');
        $category->setDescription('');
        $manager->persist($category);

        $category_sports = new Category();
        $category_sports->setName('Sports');
        $category_sports->setDescription('');
        $manager->persist($category_sports);

        $category = new Category();
        $category->setName('Entertainment');
        $category->setDescription('');
        $manager->persist($category);

        $category = new Category();
        $category->setName('Science');
        $category->setDescription('');
        $manager->persist($category);
        
        $category = new Category();
        $category->setName('Religion');
        $category->setDescription('');
        $manager->persist($category);
        
        $category = new Category();
        $category->setName('Other');
        $category->setDescription('');
        $manager->persist($category);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}