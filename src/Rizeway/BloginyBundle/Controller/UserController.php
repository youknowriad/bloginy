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
use Symfony\Component\HttpFoundation\Response;

use Rizeway\BloginyBundle\Form\ProfileForm;
use Rizeway\BloginyBundle\Form\PasswordForm;
use Rizeway\BloginyBundle\Form\AvatarForm;
use Rizeway\BloginyBundle\Form\PostProposeForm;
use Rizeway\BloginyBundle\Form\SocialNetworksForm;
use Rizeway\BloginyBundle\Model\Utils\ChangePassword;
use Rizeway\UserBundle\Lib\Utils\Avatar;
use Rizeway\BloginyBundle\Entity\Post;
use Rizeway\BloginyBundle\Entity\Vote;
use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Model\Utils\TagCloudGenerator;
use Rizeway\BloginyBundle\Model\Utils\VoteRetriever;
use Rizeway\BloginyBundle\Model\Factory\ActivityFactory;
use Rizeway\BloginyBundle\Model\Utils\SlugGenerator;
use Rizeway\BloginyBundle\Form\DataTransform\TagsTransform;
use Rizeway\BloginyBundle\Model\Utils\StringHandler;
use Rizeway\BloginyBundle\Model\Mail\LostPasswordMail;

use Rizeway\CrawlerBundle\Lib\WebPage;

class UserController extends Controller
{
    public function dashboardAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        // Tags
        $generator = new TagCloudGenerator($this->container);
        $tags      = $generator->getTagCloudForUser($user);

        return $this->render('BloginyBundle:User:dashboard.html.twig',
            array(
              'tags' => $tags,
              'user' => $user
            ));
    }

    public function profileAction($username)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));
        if (\is_null($user))
        {
            throw new NotFoundHttpException('The requested user does not exist.');
        }
        
        // Tags
        $generator = new TagCloudGenerator($this->container);
        $tags      = $generator->getTagCloudForUser($user);

        
        return $this->render('BloginyBundle:User:profile.html.twig',
            array(
                'user' => $user,
                'tags' => $tags
            ));
    }

    public function editAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        if (!$user->getLocale()) {
            $user->setLocale($this->get('request')->getSession()->getLocale());
        }
        $form = $this->get('form.factory')->create(new ProfileForm(), $user);
        $saved = false;
        
        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                $saved = true;
                $this->get('doctrine')->getEntityManager()->flush();
                
                $this->get('request')->getSession()->setLocale($user->getLocale()); 
            }
        }
        
        return $this->render('BloginyBundle:User:edit.html.twig', array(
          'form' => $form->createView(),
          'saved' => $saved,
          'user' => $user
        ));
    }

    public function avatarAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $avatar = $user->getAvatar();

        $form = $this->get('form.factory')->create(new AvatarForm(), $avatar);
        $saved = false;

        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                $saved = true;
                $avatar->update($user->getUsername().'_avatar');
                $user->setAvatar($avatar);

                $this->get('doctrine')->getEntityManager()->flush();
            }
        }

        return $this->render('BloginyBundle:User:edit_avatar.html.twig', array(
          'form'  => $form->createView(),
          'saved' => $saved,
          'user'  => $user
        ));
    }

    public function passwordAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $change = new ChangePassword();
        $form = $this->get('form.factory')->create(new PasswordForm(), $change);

        $saved = false;
        $error = null;
        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                if (!$change->checkCurrentPassword($user))
                {
                  $error = 'current_password';
                }
                else
                {
                  $user->setPassword($change->new_password);
                  $saved = true;
                  $this->get('doctrine')->getEntityManager()->flush();
                }
            }
        }

        return $this->render('BloginyBundle:User:password.html.twig', array(
          'form' => $form->createView(),
          'saved' => $saved,
          'error' => $error,
          'user' => $user
        ));
    }
    
    public function socialNetworksAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->get('form.factory')->create(new SocialNetworksForm(), $user);
        $saved = false;
        
        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                $saved = true;
                $this->get('doctrine')->getEntityManager()->flush();
            }
        }
        
        return $this->render('BloginyBundle:User:social_networks.html.twig', array(
          'form' => $form->createView(),
          'saved' => $saved,
          'user' => $user
        ));
    }
    
    public function apiCodeAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        if (is_null($user->getApiCode()))
        {
            $handler = new StringHandler();
            do
            {
                $apicode = $handler->generateRandomString('16');
                $existing = $this->getDoctrine()->getEntityManager()->getRepository('UserBundle:User')
                    ->findOneBy(array('api_code' => $apicode));
            }
            while (!is_null($existing));
                
            $user->setApiCode($apicode);
            $this->getDoctrine()->getEntityManager()->flush();
        }
        
        return $this->render('BloginyBundle:User:api_code.html.twig', array(
            'user' => $user
        ));
    }

    public function profileMenuAction($username, $dashboard = false)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));
        if (\is_null($user)) {
            throw new NotFoundHttpException('The requested user does not exist.');
        }
        
        $pages = array();
        if ($dashboard) {
            $pages = $em->getRepository('BloginyBundle:Page')->findBy(array('user' => $user->getId()));  
        }
        
        return $this->render('BloginyBundle:User:profile_menu.html.twig', 
            array(
                'user' => $user,
                'dashboard' => $dashboard,
                'pages' => $pages
            ));
    }

    public function postsAction($username, $page = 1)
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
            ->findForUser($user, $page, $this->container->getParameter('bloginy.post.max_results'));

        $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

        // Votes
        $votes = array();
        if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
        {
            $retriever = new VoteRetriever($em);
            $votes = $retriever->getVotesForPosts($this->get('security.context')->getToken()->getUser(), $posts);
        }

        return $this->render('BloginyBundle:User:posts.html.twig',
                array(
                    'posts' => $posts,
                    'votes' => $votes,
                    'user' => $user,
                    'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
                    'page' => $page + 1
                ));
    }
    
    public function activitiesAction($username, $from = 'none')
    {
        $em = $this->get('doctrine')->getEntityManager();
        $user = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));
        if (\is_null($user))
        {
            throw new NotFoundHttpException('The requested user does not exist.');
        }

        // Get The activities
        $date = new \DateTime($from == 'none' ? null : $from);
        $activities = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Activity')
            ->findForUser($user, $date, $this->container->getParameter('bloginy.activity.max_results'));
        $last_activity = (!\is_null($activities) && \count($activities))? \end($activities) : null;

        return $this->render('BloginyBundle:User:activities.html.twig',
                array(
                    'activities' => $activities,
                    'user' => $user,
                    'show_pager' => (count($activities) == $this->container->getParameter('bloginy.activity.max_results')),
                    'from' => \is_null($last_activity) ? null : $last_activity->getCreatedAt()->format('Y-m-d H:i:sP')
                ));
    }

    public function commentModerationAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        return $this->render('BloginyBundle:User:comment_moderation.html.twig', array('user' => $user));
    }
    
    public function commentsAction($filter = 'none', $page = 1)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        // Get The comments
        $comments = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Comment')
            ->findForUserModeration($user, $filter, $page, $this->container->getParameter('bloginy.comment.max_results'));

        $last_comment = (!\is_null($comments) && \count($comments))? \end($comments) : null;
        
        return $this->render('BloginyBundle:User:comments.html.twig', array(
            'comments' => $comments,
            'page'     => $page+1,
            'user'     => $user,
            'filter'   => $filter,
            'show_pager' => (count($comments) == $this->container->getParameter('bloginy.comment.max_results')),
        ));
    }
    
    public function approveCommentAction($comment_id)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->get('doctrine')->getEntityManager();
        $comment = $em->getRepository('BloginyBundle:Comment')->find($comment_id);
        if (\is_null($comment) || (!in_array(User::ROLE_ADMIN_USER, $user->getRoles()) && $comment->getPost()->getUser()->getId() !== $user->getId()))
        {
            throw new NotFoundHttpException('The requested comment does not exist.');
        }
        
        
        if (!$comment->getApproved())
        {
          $comment->approve();  
          
          $factory = new ActivityFactory();
          $activity = $factory->buildForCommentCreation($comment, $comment->getPost(), $comment->getUser());
          $em->persist($activity);
          
          $em->flush();
        }
        
        return new Response('OK');
    }
    
    public function deleteCommentAction($comment_id)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        
        $em = $this->get('doctrine')->getEntityManager();
        $comment = $em->getRepository('BloginyBundle:Comment')->find($comment_id);
        if (\is_null($comment) || (!in_array(User::ROLE_ADMIN_USER, $user->getRoles()) && $comment->getPost()->getUser()->getId() !== $user->getId()))
        {
            throw new NotFoundHttpException('The requested comment does not exist.');
        }
        
        
        if ($comment->getApproved())
        {
            $comment->getPost()->deleteApprovedComment();
        }
        
        $em->getRepository('BloginyBundle:Activity')->removeForComment($comment);
        $em->remove($comment);
        $em->flush();
                
        return new Response('OK');
    }
    
    public function blogPostProposeAction($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get  The Post
        $blogPost = $em->getRepository('BloginyBundle:BlogPost')->customFindOneBy(array('slug' => $slug));
        if (\is_null($blogPost))
        {
            throw new NotFoundHttpException('The requested post does not exist.');
        }
        
        $post = new Post();
        $post->setBlogPost($blogPost);
        $post->setTitle($blogPost->getTitle());
        $post->setLink($blogPost->getLink());
        $post->setContent($blogPost->getResume());
        
        $form = $this->get('form.factory')->create(new PostProposeForm(), $post, array(
            'tag_url' => $this->generateUrl('tag_autocomplete'),
            'tags_transformer' => new TagsTransform($post),
            'purifier_config' => $this->get('exercise_html_purifier.config.simple')
        ));
        
        return $this->render('BloginyBundle:User:blog_post_propose.html.twig', array(
            'form' => $form->createView(),
            'blog_post' => $blogPost)); 
    }
    
    public function shareAction()
    {
        return $this->render('BloginyBundle:User:share.html.twig');
    }
    
    public function writePostAction()
    {
        $em = $this->get('doctrine')->getEntityManager();
        $post = new Post();
        
        $form = $this->get('form.factory')->create(new PostProposeForm(), $post, array(
            'tag_url' => $this->generateUrl('tag_autocomplete'),
            'tags_transformer' => new TagsTransform($post),
            'write_post' => true,
            'purifier_config' => $this->get('exercise_html_purifier.config.simple')
        ));

        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));
            
            if ($form->isValid())
            {
                $user = $this->get('security.context')->getToken()->getUser();
                $post->setUser($user);
                
                // Get The Slug
                $slugGenerator = new SlugGenerator($em->getRepository('BloginyBundle:Post'));
                $post->setSlug($slugGenerator->generateUniqueSlug($post->getTitle()));

                // Get The language
                $detector = $this->get('bloginy.language_detector');
                $languages = $this->container->getParameter('bloginy.post.language');
                $text = (strlen($post->getResume()) > 20 ) ? $post->getResume() : $post->getTitle();
                $post->setLanguage($detector->detect($text, $languages));

                // Create the activity
                $factory = new ActivityFactory();
                $activity = $factory->buildForPostCreation($post, $user);
                
                // Create the vote
                $vote = new Vote();
                $vote->setPost($post);
                $vote->setUser($user);
                $post->addVotes($vote);
                
                $em->persist($post);
                $em->persist($activity);
                $em->persist($vote);
                $em->flush();
                
                return $this->render('BloginyBundle:User:post_written.html.twig');
            }
        }
        
        return $this->render('BloginyBundle:User:write_post.html.twig', array('form' => $form->createView()));    
    }
    
    public function initSharedLinkAction()
    {
        $em = $this->get('doctrine')->getEntityManager();
        $post = new Post();
  
        // Check if we have to crawl the link
        if (!is_null($this->get('request')->get('shared_link'))) {
            
            try {
                $page = new WebPage($this->get('request')->get('shared_link'));
            } 
            catch (\Exception $e)
            {
                return new Response('none');
            }
            $post->setTitle($page->getTitle());
            $post->setContent($page->getDescription());
            $post->setLink($page->getUrl());
        }
        
        // Check if we are in the case of a proposed blog post
        $blogPost = null;
        if (!is_null($this->get('request')->get('blog_post_slug'))) {
            // Get  The Post
            $blogPost = $em->getRepository('BloginyBundle:BlogPost')->customFindOneBy(array('slug' => $this->get('request')->get('blog_post_slug')));
            if (\is_null($blogPost))
            {
                throw new NotFoundHttpException('The requested post does not exist.');
            }
            $blogPost->setPost($post);
            $post->setBlogPost($blogPost);
        }
        
        $form = $this->get('form.factory')->create(new PostProposeForm(), $post, array(
            'tag_url' => $this->generateUrl('tag_autocomplete'),
            'tags_transformer' => new TagsTransform($post),
            'purifier_config' => $this->get('exercise_html_purifier.config.simple')
        ));
        
        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));
            
            if ($form->isValid())
            {
                $user = $this->get('security.context')->getToken()->getUser();
                $post->setUser($user);
                
                // Get The Slug
                $slugGenerator = new SlugGenerator($em->getRepository('BloginyBundle:Post'));
                $post->setSlug($slugGenerator->generateUniqueSlug($post->getTitle()));

                // Get The language
                $detector = $this->get('bloginy.language_detector');
                $languages = $this->container->getParameter('bloginy.post.language');
                $text = (strlen($post->getResume()) > 20 ) ? $post->getResume() : $post->getTitle();
                $post->setLanguage($detector->detect($text, $languages));

                // Create the activity
                $factory = new ActivityFactory();
                $activity = $factory->buildForPostCreation($post, $user);
                
                // Create the vote
                $vote = new Vote();
                $vote->setPost($post);
                $vote->setUser($user);
                $post->addVotes($vote);
                
                // @TODO Check the corresponding blog post
                
                $em->persist($post);
                $em->persist($activity);
                $em->persist($vote);
                $em->flush();
                
                return $this->render('BloginyBundle:User:link_shared.html.twig');
            }
        }
        
        return $this->render('BloginyBundle:User:init_shared_link.html.twig', 
            array(
                    'form' => $form->createView(),
                    'blog_post' => $blogPost
                ));    
    }
    
    public function autocompleteAction()
    {
        $request = $this->getRequest();
        $value = $request->get('term');
        $limit = $request->get('limit', 10);
        $users = $this->getDoctrine()->getRepository('UserBundle:User')->filterByUsername($value, $limit);

        $results = array();
        foreach($users as $user) {
            $results[] = array('id' => $user->getUsername(), 'label' => $user->getUsername());
        }

        $response = new Response();
        $response->setCharset('application/json');
        $response->setContent(json_encode($results));

        return $response;
    }
    
    public function lostPasswordAction()
    {
        $error = false;
        $em = $this->getDoctrine()->getEntityManager();
        if ('POST' === $this->get('request')->getMethod() && $this->get('request')->get('lost_password_email')) {
            $user = $em->getRepository('UserBundle:User')
                ->findOneBy(array('email' => $this->getRequest()->get('lost_password_email')));
        
            if (is_null($user)) {
                $error = true;
            } else {
                
                // Generate a new mail
                $handler = new StringHandler();
                $password = $handler->generateRandomString();
                $user->setPassword($password);
                
                // Send the lost password mail
                $mail = new LostPasswordMail(array('password' => $password), $user->getEmail());
                $mail->send($this->get('mailer'), $this->get('templating'));
                
                $em->flush();
                
                return $this->render('BloginyBundle:User:password_generated.html.twig');
            }
            
        }
        return $this->render('BloginyBundle:User:lost_password.html.twig', array('error' => $error));
    }
}