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
        $entry = $this->writer->createEntry();
        $entry->setTitle($blog->getTitle());
        $entry->setLink($this->container->get('router')->generate('blog_show', array('slug' => $blog->getSlug()), true));
        $entry->addAuthor(array(
            'name'  => $blog->getTitle(),
            'uri'   => $blog->getUrl(),
        ));
        $entry->setDateModified($blog->getCreatedAt()->getTimestamp());
        $entry->setDateCreated($blog->getCreatedAt()->getTimestamp());
        $entry->setDescription($blog->getDescription() ? $blog->getDescription() : 'no description');
        $entry->setContent($blog->getDescription() ? $blog->getDescription() : 'no content');
        
        $this->writer->addEntry($entry);
    }

}