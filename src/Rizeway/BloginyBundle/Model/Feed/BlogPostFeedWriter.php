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
        $item = $this->writer->add( 'item' );
        $item->title = htmlspecialchars($post->getTitle());
        $item->description = htmlspecialchars($post->getResume() ? $post->getResume() : 'no content');
        $item->published = $post->getCreatedAt();
        $item->updated = $post->getCreatedAt();
        $url = $this->container->get('router')->generate('blog_post_details', array('slug' => $post->getSlug()), true);
        $item->id = $url;

        $author = $item->add('author');
        $author->name = isset($options['author_name']) ? $options['author_name'] : $this->container->getParameter('bloginy.title');
        $author->email = isset($options['author_email']) ? $options['author_email'] : $this->container->getParameter('bloginy.email');

        $link = $item->add( 'link' );
        $link->href = $url;
        $link->rel = 'alternate';

        $link = $item->add( 'link' );
        $link->href = $url;
        $link->rel = 'self';
    }

}