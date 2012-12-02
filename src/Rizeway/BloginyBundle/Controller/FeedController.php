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

namespace Rizeway\BloginyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Rizeway\BloginyBundle\Model\Feed\PostFeedWriter;
use Rizeway\BloginyBundle\Model\Feed\BlogPostFeedWriter;
use Rizeway\BloginyBundle\Model\Feed\BlogFeedWriter;
use Rizeway\BloginyBundle\Model\Utils\Operators;


class FeedController extends Controller
{   
    public function blogPostsAction()
    {
        $em = $this->get('doctrine')->getManager();

        $posts = $em->getRepository('BloginyBundle:BlogPost')
            ->findFrom(new \DateTime(), $this->container->getParameter('bloginy.feed.max_results'));
        
        $writer = new BlogPostFeedWriter($this->container);
        $writer->addPosts($posts);
        
        return new Response($writer->generate());
    }
    
    public function topBlogsAction($location = 'all', $language = 'all')
    {
        $blogs = $this->get('doctrine')->getManager()
            ->getRepository('BloginyBundle:Blog')
            ->findTop($location, $language, 1, $this->container->getParameter('bloginy.blog.max_results'));
       
        $writer = new BlogFeedWriter($this->container);
        $writer->addBlogs($blogs);
        
        return new Response($writer->generate());
    }
}
