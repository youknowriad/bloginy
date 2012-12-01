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

use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Model\Utils\VoteRetriever;

class BlogPostController extends Controller
{
    public function listAction($from = 'none')
    {
        $em = $this->get('doctrine')->getEntityManager();

        $date = new \DateTime();
        if ($from !== 'none') {
            $date->setTimestamp($from);
        }
        $posts = $em->getRepository('BloginyBundle:BlogPost')
            ->findFrom($date, $this->container->getParameter('bloginy.post.max_results'));
        $last_post = (!\is_null($posts) && \count($posts))? \end($posts) : null;

        // Votes
        $votes = array();
        if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
        {
            $retriever = new VoteRetriever($em);
            $votes = $retriever->getVotesForBlogPosts($this->get('security.context')->getToken()->getUser(), $posts);
        }

        $view = ($this->get('request')->isXmlHttpRequest()) ? 'BloginyBundle:BlogPost:list_ajax.html.twig': 'BloginyBundle:BlogPost:list.html.twig';

        return $this->render($view,
            array(
                'votes' => $votes,
                'posts' => $posts,
                'show_pager' => (count($posts) == $this->container->getParameter('bloginy.post.max_results')),
                'from' => \is_null($last_post) ? null : $last_post->getPublishedAt()->getTimestamp()
            ));
    }

    public function detailsAction($slug)
    {
        $em = $this->get('doctrine')->getEntityManager();

        // Get  The Post
        $post = $em->getRepository('BloginyBundle:BlogPost')->customFindOneBy(array('slug' => $slug));
        if (\is_null($post))
        {
            throw new NotFoundHttpException('The requested post does not exist.');
        }

        // Get the user vote
        $vote = false;
        if ($this->get('security.context')->isGranted('ROLE_REGISTRED_USER'))
        {
            $vote = !\is_null($em->getRepository('BloginyBundle:Vote')
                ->findByUserAndBlogPost($this->get('security.context')->getToken()->getUser(), $post));
        }

        return $this->render('BloginyBundle:BlogPost:details.html.twig', array(
            'post' => $post,
            'vote' => $vote
        ));
    }
}
