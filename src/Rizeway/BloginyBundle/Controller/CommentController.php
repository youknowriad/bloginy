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

use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Entity\Comment;
use Rizeway\BloginyBundle\Form\NewCommentForm;
use Rizeway\BloginyBundle\Model\Mail\CommentModerationMail;

class CommentController extends Controller
{
    public function listAction($post_id)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get  The Post
        $post = $em->getRepository('BloginyBundle:Post')->find($post_id);
        if (\is_null($post))
        {
            throw new NotFoundHttpException('The requested post does not exist.');
        }
        
        // Get The comments
        $comments = $this->get('doctrine')->getEntityManager()
            ->getRepository('BloginyBundle:Comment')
            ->findForPost($post);


        return $this->render('BloginyBundle:Comment:list.html.twig', array('comments' => $comments));
    }

    public function newAction($post_id, $new = false)
    {
        $em = $this->get('doctrine')->getEntityManager();
        
        // Get  The Post
        $post = $em->getRepository('BloginyBundle:Post')->find($post_id);
        if (\is_null($post))
        {
            throw new NotFoundHttpException('The requested post does not exist.');
        }

        $comment = new Comment();
        $comment->setPost($post);
        $user = $this->get('security.context')->getToken()->getUser();
        if ($user instanceof User)
        {
           $comment->setUser($user);
        }

        $form = $this->get('form.factory')->create(new NewCommentForm(), $comment, array(
          'authenticated' => $user instanceof User,
          'purifier_config' => $this->get('exercise_html_purifier.config.simple')));

        if ('POST' === $this->get('request')->getMethod() && !$new) {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                
                // Save the user and the activation
                $this->get('doctrine')->getEntityManager()->persist($comment);
                $this->get('doctrine')->getEntityManager()->flush();

                // Send the comment mail to the post author
                $mail = new CommentModerationMail(array('comment' => $comment), $post->getUser()->getEmail());
                $mail->send($this->get('mailer'), $this->get('templating'));

                // Send the comment mail to the blogin admin
                $mail = new CommentModerationMail(array('comment' => $comment));
                $mail->send($this->get('mailer'), $this->get('templating'));

                // Render
                return $this->render('BloginyBundle:Comment:added.html.twig', array(
                  'comment' => $comment
                ));

            }
        }

        return $this->render('BloginyBundle:Comment:new.html.twig', array(
            'comment' => $comment,
            'form'    => $form->createView(),
            'authenticated' => $user instanceof User
            ));
    }
}
