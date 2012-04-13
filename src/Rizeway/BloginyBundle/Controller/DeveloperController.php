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

class DeveloperController extends Controller
{
    public function indexAction()
    {
        return $this->render('BloginyBundle:Developer:index.html.twig');
    }
    
    public function topPostsAction()
    {
        return $this->render('BloginyBundle:Developer:top_posts.html.twig'); 
    }
    
    public function blogPostsAction()
    {
        return $this->render('BloginyBundle:Developer:blog_posts.html.twig'); 
    }
    
    public function userPostsAction()
    {
        return $this->render('BloginyBundle:Developer:user_posts.html.twig'); 
    }
    
    public function searchPostsAction()
    {
        return $this->render('BloginyBundle:Developer:search_posts.html.twig'); 
    }
    
    public function livePostsAction()
    {
        return $this->render('BloginyBundle:Developer:live_posts.html.twig'); 
    }
    
    public function topBlogsAction()
    {
        return $this->render('BloginyBundle:Developer:top_blogs.html.twig'); 
    }
    
    public function searchBlogsAction()
    {
        return $this->render('BloginyBundle:Developer:search_blogs.html.twig'); 
    }
    
    public function errorsAction()
    {
        return $this->render('BloginyBundle:Developer:errors.html.twig'); 
    }
}
