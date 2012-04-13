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
use Symfony\Component\Form\Form;
use Rizeway\BloginyBundle\Entity\Blog;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\TextareaField;
use Symfony\Component\Form\ChoiceField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Form\BlogProposeForm;
use Rizeway\BloginyBundle\Model\Utils\SlugGenerator;
use Rizeway\BloginyBundle\Model\Mail\BlogPropositionMail;
use Rizeway\BloginyBundle\Model\Utils\Location;
use Rizeway\ExtraFrameworkBundle\Lib\Utils\BlogInfos;
use Rizeway\BloginyBundle\Model\Utils\TagCloudGenerator;

class BlogController extends Controller
{
    public function listAction($location = 'all', $language = 'all', $page = 1)
    {
        $blogs = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Blog')
            ->findTop($location, $language, $page, $this->container->getParameter('bloginy.blog.max_results'));
       
        $view = ($this->get('request')->isXmlHttpRequest()) ? 'BloginyBundle:Blog:list_ajax.html.twig': 'BloginyBundle:Blog:list.html.twig';

        return $this->render($view,
            array(
                'blogs' => $blogs,
                'location' => $location,
                'language' => $language,
                'show_pager' => (count($blogs) == $this->container->getParameter('bloginy.blog.max_results')),
                'page' => $page + 1
            ));
    }

    public function proposeAction()
    {
        $blog = new Blog();

        $form = $this->get('form.factory')->create(new BlogProposeForm(), $blog, array(
          'location_choices' => \array_combine($this->container->getParameter('bloginy.blog.location'),
                $this->container->getParameter('bloginy.blog.location')),
          'language_choices' => $this->container->getParameter('bloginy.blog.language')));

        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {

                // Save the user and the activation
                $slugGenerator = new SlugGenerator($this->get('doctrine')->getEntityManager()->getRepository('BloginyBundle:Blog'));
                $blog->setSlug($slugGenerator->generateUniqueSlug($blog->getTitle()));
                
                // Detect the language           
                $detector = $this->getContainer()->get('bloginy.language_detector');
                $languages = $this->getContainer()->getParameter('bloginy.post.language');
                $text = (strlen($blog->getDescription()) > 20 ) ? $blog->getDescription() : $blog->getTitle();
                $blog->setLanguage($detector->detect($text, $languages));
                
                $this->get('doctrine')->getEntityManager()->persist($blog);
                $this->get('doctrine')->getEntityManager()->flush();

                // Send the activation mail
                $mail = new BlogPropositionMail(array('blog' => $blog));
                $mail->send($this->get('mailer'), $this->get('templating'));

                // Redirect
                return new RedirectResponse($this->generateUrl('blog_proposed', array('slug' => $blog->getSlug())));

            }
        }
        
        return $this->render('BloginyBundle:Blog:propose.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function proposedAction($slug)
    {
        return $this->render('BloginyBundle:Blog:proposed.html.twig');
    }

    public function locationFilterAction($location = 'all', $language = 'all')
    {
        $loc= new Location();
        $loc->setLocation($location);
        $choices = array('all' => 'All locations') + \array_combine($this->container->getParameter('bloginy.blog.location'),
                $this->container->getParameter('bloginy.blog.location'));

        $form = $this->get('form.factory')
            ->createNamedBuilder('form', 'location', $loc)
            ->add('location', 'choice', array('choices' => $choices))
            ->getForm();

        return $this->render('BloginyBundle:Blog:location_filter.html.twig', array(
            'location_choices' => $form->createView(),
            'language' => $language
        ));
    }

    public function languageFilterAction($language = 'all', $location = 'all')
    {
        $languages = $this->container->getParameter('bloginy.blog.language');

        return $this->render('BloginyBundle:Blog:language_filter.html.twig', array(
            'languages' => $languages,
            'location' => $location,
            'language' => $language
        ));
    }

    public function showAction($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get  The Blog
        $blog = $em->getRepository('BloginyBundle:Blog')->customFindOneBy(array('slug' => $slug));
        if (\is_null($blog))
        {
            throw new NotFoundHttpException('The requested blog does not exist.');
        }

        // Tags
        $generator = new TagCloudGenerator($this->container);
        $tags      = $generator->getTagCloudForBlog($blog);

        // Popularity
        $max_stars      = $this->container->getParameter('bloginy.blog.max_stars');
        $max_rank_value = $em->getRepository('BloginyBundle:Blog')->getMaxRankValue();
        
        return $this->render('BloginyBundle:Blog:show.html.twig', array(
            'blog'           => $blog,
            'max_stars'      => $max_stars,
            'max_rank_value' => $max_rank_value,
            'tags'           => $tags
        ));
    }

    public function postsAction($slug, $from = 'none')
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get  The Blog
        $blog = $em->getRepository('BloginyBundle:Blog')->customFindOneBy(array('slug' => $slug));
        if (\is_null($blog))
        {
            throw new NotFoundHttpException('The requested blog does not exist.');
        }

        // Get The posts
        $date = new \DateTime($from == 'none' ? null : $from);
        $posts = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:BlogPost')
            ->findForBlog($blog, $date, $this->container->getParameter('bloginy.post.max_results'));

        // Get the Votes
        $user = $this->get('security.context')->getToken()->getUser();
        $votes = array();
        if ($user instanceof User)
        {
            $ids = \array_map(function($v) { return $v->getId(); }, $posts);
            $votes = $em->getRepository('BloginyBundle:Vote')
                ->findByUserAndBlogPosts($user, $ids);

            if (\count($votes))
            {
                $votes = \array_combine(\array_map(function($v) { return $v->getPost()->getBlogPost()->getId();}, $votes), \array_map(function($v) { return true; }, $votes));
            }
        }
        
        $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

        return $this->render('BloginyBundle:Blog:posts_list.html.twig',
                array(
                    'votes' => $votes,
                    'blog'  => $blog,
                    'posts' => $posts,
                    'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
                    'from' => \is_null($last_post) ? null : $last_post->getPublishedAt()->format('Y-m-d H:i:sP')
                ));
    }

    public function thumbnailAction($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get  The Blog
        $blog = $em->getRepository('BloginyBundle:Blog')->customFindOneBy(array('slug' => $slug));
        if (\is_null($blog))
        {
            throw new NotFoundHttpException('The requested blog does not exist.');
        }

        return $this->render('BloginyBundle:Blog:thumbnail.html.twig', array(
            'blog'           => $blog
        ));
    }

    public function googleRankingAction($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get  The Blog
        $blog = $em->getRepository('BloginyBundle:Blog')->customFindOneBy(array('slug' => $slug));
        if (\is_null($blog))
        {
            throw new NotFoundHttpException('The requested blog does not exist.');
        }

        return $this->render('BloginyBundle:Blog:google_ranking.html.twig', array(
            'pagerank'  => BlogInfos::getpagerank($blog->getUrl()),
        ));
    }

    public function alexaRankingAction($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get  The Blog
        $blog = $em->getRepository('BloginyBundle:Blog')->customFindOneBy(array('slug' => $slug));
        if (\is_null($blog))
        {
            throw new NotFoundHttpException('The requested blog does not exist.');
        }

        return $this->render('BloginyBundle:Blog:alexa_ranking.html.twig', array(
            'popularity' => BlogInfos::get_alexa_popularity($blog->getUrl()),
            'backlinks'  => BlogInfos::alexa_backlink($blog->getUrl())
        ));
    }
    
    public function dailyBlogAction()
    {
        $em = $this->get('doctrine')->getEntityManager();
        
        // Get The Daily Blog
        $blog = $em->getRepository('BloginyBundle:Blog')->findDailyBlog();
        
        return $this->render('BloginyBundle:Blog:daily_blog.html.twig', array('blog' => $blog));
    }
    
    public function autocompleteAction()
    {
        $request = $this->getRequest();
        $value = $request->get('term');
        $limit = $request->get('limit', 10);
        $blogs = $this->getDoctrine()->getRepository('BloginyBundle:Blog')->filterByTitle($value, $limit);

        $results = array();
        foreach($blogs as $blog) {
            $results[] = array('id' => $blog->getSlug(), 'label' => $blog->getShortTitle(35));
        }

        $response = new Response();
        $response->setCharset('application/json');
        $response->setContent(json_encode($results));

        return $response;
    }
    
    public function lastAction()
    {
        $blogs = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Blog')
            ->findLast($this->container->getParameter('bloginy.blog.sidebar.max_results'));
       
        return $this->render('BloginyBundle:Blog:last.html.twig',array('blogs' => $blogs));
    }
    
  public function searchAction()
  {
      $filter = $this->getRequest()->get('filter');
     
      return $this->render('BloginyBundle:Blog:search.html.twig', array('filter'  => $filter));
  }
  
  public function searchBlogsAction($filter, $page = 1)
  {
      $em = $this->getDoctrine()->getEntityManager();
      $blogs = $em->getRepository('BloginyBundle:Blog')
            ->search($filter, $page, $this->container->getParameter('bloginy.blog.max_results'));
       
      return $this->render('BloginyBundle:Blog:search_ajax.html.twig',
            array(
                'blogs' => $blogs,
                'filter' => $filter,
                'show_pager' => (count($blogs) == $this->container->getParameter('bloginy.blog.max_results')),
                'page' => $page + 1
            ));
  }
}