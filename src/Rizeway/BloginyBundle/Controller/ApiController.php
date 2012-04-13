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

use Rizeway\BloginyBundle\Model\Utils\Operators;
use Rizeway\BloginyBundle\Model\Repository\PostRepository;

class ApiController extends Controller
{
    public function topPostsAction($code, $client)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $authenticator = $this->get('bloginy.api_authenticator');
        
        if (!$authenticator->login($code, $client, 'topFeeds')) {
            return $this->render('BloginyBundle:Api:error_authentication.xml.twig');
        }
        
        $posts = $em->getRepository('BloginyBundle:Post')
            ->findTop(null, PostRepository::SORT_TOP, 'all', $this->getRequest()->get('page', 1), $this->container->getParameter('bloginy.post.max_results'));

        return $this->render('BloginyBundle:Api:posts.xml.twig', array('posts' => $posts));
            
    }
    
    public function searchPostsAction($code, $client, $query)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $authenticator = $this->get('bloginy.api_authenticator');
        
        if (!$authenticator->login($code, $client, 'searchFeeds')) {
            return $this->render('BloginyBundle:Api:error_authentication.xml.twig');
        }
      
        $posts = $em->getRepository('BloginyBundle:Post')->search($query, $this->getRequest()->get('page', 1), $this->container->getParameter('bloginy.post.max_results'));
      
        return $this->render('BloginyBundle:Api:posts.xml.twig', array('posts' => $posts));
            
    }
    
    public function userPostsAction($code, $client, $username)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $authenticator = $this->get('bloginy.api_authenticator');
        
        if (!$authenticator->login($code, $client, 'userFeeds')) {
            return $this->render('BloginyBundle:Api:error_authentication.xml.twig');
        }
      
        $user = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));
        if (\is_null($user)) {
            return $this->render('BloginyBundle:Api:error_user.xml.twig');
        }

        // Get The posts
        $posts = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Post')
            ->findForUser($user, $this->getRequest()->get('page', 1), $this->container->getParameter('bloginy.post.max_results'));
        
        return $this->render('BloginyBundle:Api:user_posts.xml.twig', array('user' => $user, 'posts' => $posts));
            
    }
    
    public function blogPostsAction($code, $client, $id)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $authenticator = $this->get('bloginy.api_authenticator');
        
        if (!$authenticator->login($code, $client, 'blogFeeds')) {
            return $this->render('BloginyBundle:Api:error_authentication.xml.twig');
        }
      
        // Get  The Blog
        $blog = $em->getRepository('BloginyBundle:Blog')->customFindOneBy(array('id' => $id));
        if (\is_null($blog)) {
            return $this->render('BloginyBundle:Api:error_blog.xml.twig');
        }

        // Get The posts
        $posts = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Post')
            ->findForBlog($blog, $this->getRequest()->get('page', 1), $this->container->getParameter('bloginy.post.max_results'));
        
        return $this->render('BloginyBundle:Api:blog_posts.xml.twig', array('blog' => $blog, 'posts' => $posts));
    }
    
    public function livePostsAction($code, $client, $from = 'none')
    {
        $em = $this->get('doctrine')->getEntityManager();
        $authenticator = $this->get('bloginy.api_authenticator');
        
        if (!$authenticator->login($code, $client, 'liveFeeds')) {
            return $this->render('BloginyBundle:Api:error_authentication.xml.twig');
        }
        
        $date = new \DateTime($from == 'none' ? null : $from);
        $posts = $em->getRepository('BloginyBundle:Post')
            ->findFrom($date, $this->container->getParameter('bloginy.post.max_results'), Operators::OPERATOR_LESS_THAN);
        
        return $this->render('BloginyBundle:Api:posts.xml.twig', array('posts' => $posts));
    }
    
    public function topBlogsAction($code, $client)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $authenticator = $this->get('bloginy.api_authenticator');
        
        if (!$authenticator->login($code, $client, 'topBlogs')) {
            return $this->render('BloginyBundle:Api:error_authentication.xml.twig');
        }
        
        $blogs = $em->getRepository('BloginyBundle:Blog')
            ->findTop('all', 'all', $this->getRequest()->get('page', 1), $this->container->getParameter('bloginy.blog.max_results'));
       
        return $this->render('BloginyBundle:Api:blogs.xml.twig', array('blogs' => $blogs));
        
    }
    
    public function searchBlogsAction($code, $client, $query)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $authenticator = $this->get('bloginy.api_authenticator');
        
        if (!$authenticator->login($code, $client, 'searchBlogs')) {
            return $this->render('BloginyBundle:Api:error_authentication.xml.twig');
        }
        
        $blogs = $em->getRepository('BloginyBundle:Blog')
            ->search($query, $this->getRequest()->get('page', 1), $this->container->getParameter('bloginy.blog.max_results'));
        
        return $this->render('BloginyBundle:Api:blogs.xml.twig', array('blogs' => $blogs));
    }
}
