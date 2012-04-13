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

namespace Rizeway\BloginyBundle\Model\Feed;

use Rizeway\BloginyBundle\Entity\BlogPost;

class BlogPostFeedWriter extends ContainerAwareFeedWriter {

    /**
     *
     * @param BlogPost[] $posts 
     */
    public function addPosts($posts) 
    {
        foreach ($posts as $post) 
        {
            $this->addPost($post);
        }
    }

    public function addPost(BlogPost $post) 
    {
        $entry = $this->writer->createEntry();
        $entry->setTitle($post->getTitle());
        $entry->setLink($this->container->get('router')->generate('blog_post_show', array('slug' => $post->getSlug()), true));
        $entry->addAuthor(array(
            'name'  => $post->getBlog()->getTitle(),
            'uri'   => $this->container->get('router')->generate('blog_show', array('slug' => $post->getBlog()->getSlug()), true),
        ));
        $entry->setDateModified($post->getCreatedAt()->getTimestamp());
        $entry->setDateCreated($post->getCreatedAt()->getTimestamp());
        $entry->setDescription($post->getResume() ? $post->getResume() : 'no description');
        $entry->setContent($post->getResume() ? $post->getResume() : 'no content');
        
        $this->writer->addEntry($entry);
    }

}