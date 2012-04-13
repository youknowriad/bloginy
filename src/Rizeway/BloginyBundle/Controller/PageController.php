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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Rizeway\BloginyBundle\Entity\Page;
use Rizeway\BloginyBundle\Form\NewPageForm;
use Rizeway\BloginyBundle\Form\DataTransform\PageTagsTransform;
use Rizeway\BloginyBundle\Form\DataTransform\BlogsTransform;
use Rizeway\BloginyBundle\Form\DataTransform\UsersTransform;
use Rizeway\BloginyBundle\Model\Utils\SlugGenerator;
use Rizeway\BloginyBundle\Model\Utils\VoteRetriever;
use Rizeway\BloginyBundle\Model\Factory\ActivityFactory;

class PageController extends Controller
{
    public function newAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine')->getEntityManager();
        if (count($em->getRepository('BloginyBundle:Page')->findBy(array('user' => $user->getId()))) >= 10)
        {
            throw new AccessDeniedHttpException('You reached your maximum number of pages');
        }
            
        $page = new Page();
        $page->setUser($user);
        
        $form = $this->get('form.factory')->create(new NewPageForm(), $page, array(
            'tag_url' => $this->generateUrl('tag_autocomplete'),
            'tags_transformer' => new PageTagsTransform($page),
            'blog_url' => $this->generateUrl('blog_autocomplete'),
            'blogs_transformer' => new BlogsTransform($em),
            'user_url' => $this->generateUrl('user_autocomplete'),
            'users_transformer' => new UsersTransform($em)
        ));
        
        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));
            
            if ($form->isValid())
            {
                $user = $this->get('security.context')->getToken()->getUser();
                $page->setUser($user);
                
                // Get The Slug
                $slugGenerator = new SlugGenerator($this->get('doctrine')->getEntityManager()->getRepository('BloginyBundle:Page'));
                $page->setSlug($slugGenerator->generateUniqueSlug($page->getName()));
               
                // Create the activity
                if ($page->getPublic())
                {
                    $factory = new ActivityFactory();
                    $activity = $factory->buildForPageCreation($page, $user);  
                }
                
                
                $em->persist($page);
                $em->persist($activity);
                $em->flush();
                
                $this->get('session')->setFlash('notice', 'Your page has been created successfully!');
                
                return $this->render('BloginyBundle:Page:created.html.twig', array('page' => $page));
            }
        }
        
        return $this->render('BloginyBundle:Page:new.html.twig', array(
            'form' => $form->createView()
        ));        
    }
    
    public function showAction($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $page = $em->getRepository('BloginyBundle:Page')->findOneBy(array('slug' => $slug));
        if (\is_null($page))
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        if (!$page->getPublic() && $this->get('security.context')->isGranted('ROLE_REGISTRED_USER')
                && $user->getUsername() !== $page->getUser()->getUsername())
        {
            throw new AccessDeniedHttpException('You are not allowed to view this page');
        }
        
        $view = $this->get('request')->isXmlHttpRequest() ? 'BloginyBundle:Page:show_ajax.html.twig' : 'BloginyBundle:Page:show.html.twig';
        
        return $this->render($view, array(
            'page' => $page
        ));
    }
    
    public function tagPostsAction($slug, $p = 1)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $page = $em->getRepository('BloginyBundle:Page')->findOneBy(array('slug' => $slug));
        if (\is_null($page))
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        if (!$page->getPublic() && $this->get('security.context')->isGranted('ROLE_REGISTRED_USER')
                && $user->getUsername() !== $page->getUser()->getUsername())
        {
            throw new AccessDeniedHttpException('You are not allowed to view this page');
        }
        
        // Get The posts
        $posts = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Post')
            ->findForTag($page->getTagsArray(), $p, $this->container->getParameter('bloginy.post.max_results'));
        $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

        // Votes
        $votes = array();
        if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
        {
            $retriever = new VoteRetriever($em);
            $votes = $retriever->getVotesForPosts($this->get('security.context')->getToken()->getUser(), $posts);
        }

        return $this->render('BloginyBundle:Page:tag_posts.html.twig',
                array(
                    'posts' => $posts,
                    'votes' => $votes,
                    'page'  => $page,
                    'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
                    'p' => $p + 1
                ));
    }
    
    public function blogPostsAction($slug, $from = 'none')
    {
        $em = $this->get('doctrine')->getEntityManager();
        $page = $em->getRepository('BloginyBundle:Page')->findOneBy(array('slug' => $slug));
        if (\is_null($page))
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        if (!$page->getPublic() && $this->get('security.context')->isGranted('ROLE_REGISTRED_USER')
                && $user->getUsername() !== $page->getUser()->getUsername())
        {
            throw new AccessDeniedHttpException('You are not allowed to view this page');
        }
        
        // Get The posts
        $date = new \DateTime($from == 'none' ? null : $from);
        $posts = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:BlogPost')
            ->findForBlog($page->getBlogs(), $date, $this->container->getParameter('bloginy.post.max_results'));
        $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

        // Votes
        $votes = array();
        if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
        {
            $retriever = new VoteRetriever($em);
            $votes = $retriever->getVotesForBlogPosts($this->get('security.context')->getToken()->getUser(), $posts);
        }

        return $this->render('BloginyBundle:Page:blog_posts.html.twig',
                array(
                    'posts' => $posts,
                    'votes' => $votes,
                    'page'  => $page,
                    'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
                    'from' => \is_null($last_post) ? null : $last_post->getPublishedAt()->format('Y-m-d H:i:sP')
                ));
    }
    
    public function userPostsAction($slug, $p = 1)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $page = $em->getRepository('BloginyBundle:Page')->findOneBy(array('slug' => $slug));
        if (\is_null($page))
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        
        $user = $this->get('security.context')->getToken()->getUser();
        if (!$page->getPublic() && $this->get('security.context')->isGranted('ROLE_REGISTRED_USER')
                && $user->getUsername() !== $page->getUser()->getUsername())
        {
            throw new AccessDeniedHttpException('You are not allowed to view this page');
        }
        
        // Get The posts
        $posts = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Post')
            ->findForUser($page->getUsers(), $p, $this->container->getParameter('bloginy.post.max_results'));
        $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

        // Votes
        $votes = array();
        if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
        {
            $retriever = new VoteRetriever($em);
            $votes = $retriever->getVotesForPosts($this->get('security.context')->getToken()->getUser(), $posts);
        }

        return $this->render('BloginyBundle:Page:user_posts.html.twig',
                array(
                    'posts' => $posts,
                    'votes' => $votes,
                    'page'  => $page,
                    'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
                    'p' => $p + 1
                ));
    }

    public function dzblogdayAction($page = 1)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get The posts
        $posts = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Post')
            ->findForTag($this->container->getParameter('bloginy.dzblogday.tags'), $page, $this->container->getParameter('bloginy.post.max_results'));
        // Votes
        $votes = array();
        if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
        {
            $retriever = new VoteRetriever($em);
            $votes = $retriever->getVotesForPosts($this->get('security.context')->getToken()->getUser(), $posts);
        }


        $view = ($this->get('request')->isXmlHttpRequest()) ? 'BloginyBundle:Page:dzblogday_ajax.html.twig' : 'BloginyBundle:Page:dzblogday.html.twig';

        return $this->render($view,
            array(
                'posts' => $posts,
                'votes' => $votes,
                'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
                'page' => $page + 1
            ));
    }
}
