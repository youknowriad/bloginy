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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Rizeway\BloginyBundle\Model\Repository\PostRepository;
use Rizeway\BloginyBundle\Model\Utils\LanguageDetector;
use Rizeway\BloginyBundle\Entity\Vote;
use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Model\Utils\TagCloudGenerator;
use Rizeway\BloginyBundle\Model\Utils\VoteRetriever;
use Rizeway\BloginyBundle\Model\Utils\Operators;
use Rizeway\BloginyBundle\Model\Factory\ActivityFactory;

class PostController extends Controller
{

  // compatibility
  public function oldShowAction($id)
  {
    $em = $this->get('doctrine')->getEntityManager();
    
    // Get  The Post
    $post = $em->getRepository('BloginyBundle:Post')->find($id);
    if (\is_null($post))
    {
        throw new NotFoundHttpException('The requested post does not exist.');
    }
    
    return $this->forward('BloginyBundle:Post:details', array('slug' => $post->getSlug()));
  }
  
  public function showAction($slug)
  {
    $em = $this->get('doctrine')->getEntityManager();
    
    // Get  The Post
    $post = $em->getRepository('BloginyBundle:Post')->customFindOneBy(array('slug' => $slug));
    if (\is_null($post))
    {
        throw new NotFoundHttpException('The requested post does not exist.');
    }
    
    if ( \is_null($post->getLink()) || \trim($post->getLink()) == '')
    {
        return $this->redirect($this->generateUrl('post_details', array('slug' => $slug)));
    }
    
    $this->get('bloginy.visit_handler')->checkVisit($post);

    // Get the user vote
    $vote = false;
    if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
    {
      $vote = !\is_null($em->getRepository('BloginyBundle:Vote')
            ->findByUserAndPost($this->get('security.context')->getToken()->getUser(), $post));
    }

    return $this->render('BloginyBundle:Post:show.html.twig', array('post' => $post, 'vote' => $vote));
  }

  public function detailsAction($slug)
  {
    $em = $this->get('doctrine')->getEntityManager();

    // Get  The Post
    $post = $em->getRepository('BloginyBundle:Post')->customFindOneBy(array('slug' => $slug));
    if (\is_null($post))
    {
        throw new NotFoundHttpException('The requested post does not exist.');
    }

    $this->get('bloginy.visit_handler')->checkVisit($post);
    
    // Get the user vote
    $vote = false;
    if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
    {
      $vote = !\is_null($em->getRepository('BloginyBundle:Vote')
            ->findByUserAndPost($this->get('security.context')->getToken()->getUser(), $post));
    }

    return $this->render('BloginyBundle:Post:details.html.twig', array('post' => $post, 'vote' => $vote));
  }

  public function listAction($category_name = 'none', $sort = PostRepository::SORT_TOP, $language = 'all', $page = 1)
  {
      $em = $this->get('doctrine')->getEntityManager();
      $category = $em->getRepository('BloginyBundle:Category')
            ->findOneBy(array('name' => $category_name));

      $posts = $em->getRepository('BloginyBundle:Post')
            ->findTop($category, $sort, $language, $page, $this->container->getParameter('bloginy.post.max_results'));

      // Votes
      $votes = array();
      if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
      {
          $retriever = new VoteRetriever($em);
         $votes = $retriever->getVotesForPosts($this->get('security.context')->getToken()->getUser(), $posts);
      }

      // Tags
      $tags = array();
      if (!$this->get('request')->isXmlHttpRequest())
      {
        $generator = new TagCloudGenerator($this->container);
        $tags      = $generator->getTagCloudForTopPosts($category, $sort, $language);
      }      

      $view = ($this->get('request')->isXmlHttpRequest()) ? 'BloginyBundle:Post:list_ajax.html.twig' : 'BloginyBundle:Post:list.html.twig';

      return $this->render($view,
         array(
          'posts' => $posts,
          'votes' => $votes,
          'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
          'page' => $page + 1,
          'category_name' => is_null($category) ? 'none' : $category->getName(),
          'sort' => $sort,
          'language' => $language,
          'tags' => $tags
      ));
  }

  public function languageFilterAction($language = 'all', $category_name = 'none', $sort = PostRepository::SORT_TOP)
  {
    $languages = $this->container->getParameter('bloginy.post.language');

    return $this->render('BloginyBundle:Post:language_filter.html.twig', array(
      'languages' => $languages,
      'category_name' => $category_name,
      'sort' => $sort,
      'language' => $language
    ));
    }

  public function voteAction($slug)
  {
    $em = $this->get('doctrine')->getEntityManager();
    $post = $em->getRepository('BloginyBundle:Post')
            ->findOneBy(array('slug' => $slug));

    if (\is_null($post))
    {
        throw new NotFoundHttpException('The requested post does not exist.');
    }

    $user = $this->get('security.context')->getToken()->getUser();

    if (!($user instanceof User))
    {
      throw new \Exception('User not logged in');
    }

    $vote = $em->getRepository('BloginyBundle:Vote')
            ->findByUserAndPost($user, $post);

    if (\is_null($vote))
    {
      $vote = new Vote();
      $vote->setPost($post);
      $vote->setUser($user);
      $post->addVotes($vote);

      $activity_factory = new ActivityFactory();
      $activity = $activity_factory->buildForVote($post, $user);
      
      $em->persist($activity);
      $em->persist($vote);
      $em->flush();
    }

    if ($this->get('request')->isXmlHttpRequest())
    {
      return $this->render('BloginyBundle:Post:count_votes.html.twig', array(
        'post' => $post
      ));
    }

    $route = (is_null($post->getLink()) || $post->getLink() == '') ? 'post_details' : 'post_show';
    return new RedirectResponse($this->generateUrl($route, array('slug' => $post->getSlug())));
  }

  public function liveAction($from = 'none', $refresh = false)
  {
     $em = $this->get('doctrine')->getEntityManager();

     $date = new \DateTime($from == 'none' ? null : $from);
     $posts = $em->getRepository('BloginyBundle:Post')
        ->findFrom($date, $this->container->getParameter('bloginy.post.max_results'),
            $refresh ? Operators::OPERATOR_GREATER_THAN : Operators::OPERATOR_LESS_THAN);
     $first_post = (!\is_null($posts) && \count($posts))? \reset($posts) : null;
     $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

     // Votes
     $votes = array();
     if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
     {
         $retriever = new VoteRetriever($em);
         $votes = $retriever->getVotesForPosts($this->get('security.context')->getToken()->getUser(), $posts);
     }

           // Tags
      $tags = array();
      if (!$this->get('request')->isXmlHttpRequest())
      {
        $generator = new TagCloudGenerator($this->container);
        $tags      = $generator->getTagCloudForLive();
      }     

     $view = ($this->get('request')->isXmlHttpRequest()) ? 'BloginyBundle:Post:live_ajax.html.twig': 'BloginyBundle:Post:live.html.twig';

     return $this->render($view,
         array(
             'refreshed' => true,
             'refresh' => $refresh,
             'votes' => $votes,
             'posts' => $posts,
             'tags'  => $tags,
             'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
             'from' => \is_null($last_post) ? null : $last_post->getCreatedAt()->format('Y-m-d H:i:sP'),
             'refresh_from' => \is_null($first_post) ? (($refresh) ? $from : null) : $first_post->getCreatedAt()->format('Y-m-d H:i:sP')
         ));
  }

  public function tagAction($tag, $page = 1)
  {
      $em = $this->get('doctrine')->getEntityManager();
      
      $posts = $em->getRepository('BloginyBundle:Post')->findForTag($tag, $page, $this->container->getParameter('bloginy.post.max_results'));
      $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

      // Votes
      $votes = array();
      if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
      {
         $retriever = new VoteRetriever($em);
         $votes = $retriever->getVotesForPosts($this->get('security.context')->getToken()->getUser(), $posts);
      }

      // Tags
      $tags = array();
      if (!$this->get('request')->isXmlHttpRequest())
      {
        $generator = new TagCloudGenerator($this->container);
        $tags      = $generator->getTagCloudForTag($tag);
      }

      $view = ($this->get('request')->isXmlHttpRequest()) ? 'BloginyBundle:Post:tag_ajax.html.twig': 'BloginyBundle:Post:tag.html.twig';

      return $this->render($view,
         array(
             'votes' => $votes,
             'posts' => $posts,
             'tags'  => $tags,
             'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
             'page' => $page + 1,
             'tag'  => $tag
         ));
  }
  
  public function lastAction()
  {
     $em = $this->get('doctrine')->getEntityManager();
     $posts = $em->getRepository('BloginyBundle:Post')
        ->findFrom(new \DateTime(), $this->container->getParameter('bloginy.post.sidebar.max_results'), Operators::OPERATOR_LESS_THAN);

     return $this->render('BloginyBundle:Post:last.html.twig', array('posts' => $posts));
  }
  
  public function searchAction()
  {
      $filter = $this->getRequest()->get('filter');
      
      // Tags
      $tags = array();
      if (!$this->get('request')->isXmlHttpRequest())
      {
        $generator = new TagCloudGenerator($this->container);
        $tags      = $generator->getTagCloudForSearchFilter($filter);
      }
      
      return $this->render('BloginyBundle:Post:search.html.twig', array(
          'filter'  => $filter,
          'tags' => $tags));
  }
  
  public function searchPostsAction($filter, $page = 1)
  {
      $em = $this->getDoctrine()->getEntityManager();
      $posts = $em->getRepository('BloginyBundle:Post')->search($filter, $page, $this->container->getParameter('bloginy.post.max_results'));
      $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

      // Votes
      $votes = array();
      if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
      {
         $retriever = new VoteRetriever($em);
         $votes = $retriever->getVotesForPosts($this->get('security.context')->getToken()->getUser(), $posts);
      }

      return $this->render('BloginyBundle:Post:search_ajax.html.twig',
         array(
             'votes' => $votes,
             'posts' => $posts,
             'filter'  => $filter,
             'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
             'page' => $page + 1,
         ));
  }

}
