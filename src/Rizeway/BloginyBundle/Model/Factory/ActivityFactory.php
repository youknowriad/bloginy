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

namespace Rizeway\BloginyBundle\Model\Factory;

use Rizeway\BloginyBundle\Entity\Comment;
use Rizeway\BloginyBundle\Entity\Post;
use Rizeway\UserBundle\Entity\User;
use Rizeway\BloginyBundle\Entity\Blog;
use Rizeway\BloginyBundle\Entity\Activity;
use Rizeway\BloginyBundle\Entity\Page;

class ActivityFactory
{
    /**
     *
     * @param Comment $comment
     * @param Post $post
     * @param User $user
     * @return Activity 
     */
    public function buildForCommentCreation(Comment $comment, Post $post, User $user = null)
    {
        $activity = new Activity();
        $activity->setComment($comment);
        $activity->setPost($post);
        $activity->setType(Activity::TYPE_COMMENT_CREATION);
        
        if (!is_null($user))
        {
            $activity->setUser($user);
        }
        
        return $activity;
    }
    
    /**
     *
     * @param Post $post
     * @param User $user
     * @return Activity 
     */
    public function buildForPostCreation(Post $post, User $user)
    {
        $activity = new Activity();
        $activity->setPost($post);
        $activity->setUser($user);
        $activity->setType(Activity::TYPE_POST_CREATION);
        
        return $activity;
    }
    
    /**
     *
     * @param Blog $blog
     * @return Activity 
     */
    public function buildForBlogCreation(Blog $blog)
    {
        $activity = new Activity();
        $activity->setBlog($blog);
        $activity->setType(Activity::TYPE_BLOG_CREATION);
        
        return $activity;
    }
    
    /**
     *
     * @param Post $post
     * @param User $user
     * @return Activity 
     */
    public function buildForVote(Post $post, User $user)
    {
        $activity = new Activity();
        $activity->setPost($post);
        $activity->setUser($user);
        $activity->setType(Activity::TYPE_VOTE);
        
        return $activity;
    }
    
    /**
     *
     * @param User $user
     * @return Activity 
     */
    public function buildForUserCreation(User $user)
    {
        $activity = new Activity();
        $activity->setUser($user);
        $activity->setType(Activity::TYPE_USER_CREATION);
        
        return $activity;
    
    }
    
    /**
     *
     * @param Page $page
     * @param User $user
     * @return Activity 
     */
    public function buildForPageCreation(Page $page, User $user)
    {
        $activity = new Activity();
        $activity->setPage($page);
        $activity->setUser($user);
        $activity->setType(Activity::TYPE_PAGE_CREATION);
        
        return $activity;
    }
}
