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
use Rizeway\BloginyBundle\Entity\BlogPost;
use Doctrine\Common\Persistence\ObjectManager;

class BlogPostData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $blog = $this->getReference('youknowriad-blog');
        $post = $this->getReference('blog-post-from-youknowriad');

        $blogpost = new BlogPost();
        $blogpost->setTitle($post->getTitle());
        $blogpost->setBlog($blog);
        $blogpost->setContent($post->getContent());
        $blogpost->setLanguage($post->getLanguage());
        $blogpost->setLink($post->getLink());
        $blogpost->setPublishedAt($post->getCreatedAt());
        $blogpost->setPost($post);
        $blogpost->setSlug($post->getSlug());

        $post->setBlogPost($blogpost);

        $manager->persist($blogpost);
        $manager->flush();
    }

    public function getOrder()
    {
        return 6;
    }
}