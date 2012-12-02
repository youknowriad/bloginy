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

use Rizeway\BloginyBundle\Entity\Blog;

class BlogFeedWriter extends ContainerAwareFeedWriter {

    /**
     *
     * @param Blog[] $posts 
     */
    public function addBlogs($blogs) 
    {
        foreach ($blogs as $blog) 
        {
            $this->addblog($blog);
        }
    }

    public function addBlog(Blog $blog) 
    {
        $item = $this->writer->add( 'item' );
        $item->title = htmlspecialchars($blog->getTitle());
        $item->description = htmlspecialchars($blog->getDescription() ? $blog->getDescription() : 'no content');
        $item->published = $blog->getCreatedAt();
        $item->updated = $blog->getCreatedAt();
        $url = $this->container->get('router')->generate('blog_show', array('slug' => $blog->getSlug()), true);
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