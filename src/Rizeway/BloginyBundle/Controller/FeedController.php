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
    public function topPostsAction($category_name = 'none', $sort = PostRepository::SORT_TOP, $language = 'all')
    {
        $em = $this->get('doctrine')->getEntityManager();
        $category = $em->getRepository('BloginyBundle:Category')
            ->findOneBy(array('name' => $category_name));

        $posts = $em->getRepository('BloginyBundle:Post')
            ->findTop($category, $sort, $language, 1, $this->container->getParameter('bloginy.feed.max_results'));
        
        $writer = new PostFeedWriter($this->container);
        $writer->addPosts($posts);
        
        return new Response($writer->generate());
    }
    
    public function newPostsAction()
    {
        $em = $this->get('doctrine')->getEntityManager();
     
        $date = new \DateTime();
        $posts = $em->getRepository('BloginyBundle:Post')
            ->findFrom($date, $this->container->getParameter('bloginy.feed.max_results'), Operators::OPERATOR_LESS_THAN);
        
        $writer = new PostFeedWriter($this->container);
        $writer->addPosts($posts);
        
        return new Response($writer->generate());
    }
    
    public function userPostsAction($username)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));
        if (\is_null($user))
        {
            throw new NotFoundHttpException('The requested user does not exist.');
        }

        // Get The posts
        $posts = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Post')
            ->findForUser($user, 1, $this->container->getParameter('bloginy.feed.max_results'));
        
        $writer = new PostFeedWriter($this->container);
        $writer->addPosts($posts);
        
        return new Response($writer->generate());
    }
    
    public function blogPostsAction()
    {
        $em = $this->get('doctrine')->getEntityManager();

        $posts = $em->getRepository('BloginyBundle:BlogPost')
            ->findFrom(new \DateTime(), $this->container->getParameter('bloginy.feed.max_results'));
        
        $writer = new BlogPostFeedWriter($this->container);
        $writer->addPosts($posts);
        
        return new Response($writer->generate());
    }
    
    public function topBlogsAction($location = 'all', $language = 'all')
    {
        $blogs = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Blog')
            ->findTop($location, $language, 1, $this->container->getParameter('bloginy.blog.max_results'));
       
        $writer = new BlogFeedWriter($this->container);
        $writer->addBlogs($blogs);
        
        return new Response($writer->generate());
    }
}
