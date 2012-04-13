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

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use \Rizeway\BloginyBundle\Entity\Comment;
use Doctrine\Common\Persistence\ObjectManager;

class CommentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = $this->getReference('admin-user');
        $post = $this->getReference('commented-post');
        
        $comment = new Comment();
        $comment->setUsername('Mohammed');
        $comment->setEmail('testmohammed@gmail.com');
        $comment->setComment('Nice bost, keep the good job.');
        $comment->setPost($post);
        $comment->approve();

        $manager->persist($comment);

        $comment2 = new Comment();
        $comment2->setComment('L’ajout de tags est plus visible, il a été remonté au niveau du formulaire de proposition d’un article. La fonction d’autocomplete fait également son apparition, puisque vous serez guidés avec les tags les plus utilisés sur bloginy au fur et à mesure que vous entrez vos tags..');
        $comment2->setPost($post);
        $comment2->setUser($user);
        $comment2->approve();

        $manager->persist($comment2);

        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}