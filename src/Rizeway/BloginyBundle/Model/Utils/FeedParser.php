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

use Rizeway\BloginyBundle\Entity\BlogPost;

class FeedParser
{
    /**
     *
     * @param string $feed_url
     * @return BlogPost[]
     */
    public function parse($feed_url)
    {
        $simplepie = new \SimplePie();
        $simplepie->enable_cache(false);
        $simplepie->enable_order_by_date();
        $simplepie->set_feed_url($feed_url);
        $simplepie->init();
        $simplepie->handle_content_type();

        $posts = array();
        foreach ($simplepie->get_items() as $item)
        {
            $title = $this->sanitize($item->get_title());
            $content = $this->sanitize($item->get_content());

            if (\strlen($title) && \strlen($content))
            {
              $post = new BlogPost();
              $post->setTitle($title);
              $post->setContent($content);
              $post->setLink($item->get_permalink());
              $post->setPublishedAt(new \DateTime($item->get_date()));

              $posts[] = $post;
            }
        }

        return $posts;
    }

    /**
     *
     * @param string $input
     */
    private function sanitize($input)
    {
        return trim($input);
    }
}