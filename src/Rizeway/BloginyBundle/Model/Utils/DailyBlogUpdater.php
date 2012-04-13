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

namespace Rizeway\BloginyBundle\Model\Utils;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;

use Rizeway\BloginyBundle\Entity\Blog;
use Rizeway\BloginyBundle\Entity\DailyBlog;

class DailyBlogUpdater
{
    /**
     * Doctrine Entity Manager
     * 
     * @var EntityManager
     */
    private $entity_manager;

    public function __construct(EntityManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    /**
     * Update the daily blog
     * 
     * @return Blog
     */
    public function updateDailyBlog()
    {
        // Reset current daily blog
        $old = $this->entity_manager->getRepository('BloginyBundle:DailyBlog')->resetCurrentDailyBlog();

        $blog = null;
        $now = new \DateTime();
        $date_limit = new \DateTime();
        $date_limit->modify('-2 month');
        while (!$blog && $now->getTimestamp() > $date_limit->getTimestamp())
        {
            $blog = $this->selectNewDailyBlog($date_limit);
            $date_limit->modify('+15 days');
        }
        
        if (!$blog)
        {
            $blog = $this->selectNewDailyBlog($now);
        }
        
        if ($blog)
        {
            // Create the daily blog entry
            $daily_blog = new DailyBlog();
            $daily_blog->setActive(true);
            $daily_blog->setBlog($blog);

            $this->entity_manager->persist($daily_blog);
        }

        $this->entity_manager->flush();
        
        return $blog;
    }
    
    private function selectNewDailyBlog(\DateTime $date_limit_ignored)
    {
        // Get The new DailyBlog        
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('BloginyBundle:Blog', 'blog');
        $rsm->addFieldResult('blog', 'id', 'id');
        $rsm->addFieldResult('blog', 'slug', 'slug');
        $rsm->addFieldResult('blog', 'title', 'title');
        $rsm->addFieldResult('blog', 'description', 'description');
        $query = $this->entity_manager->createNativeQuery('SELECT Blog.id, Blog.slug, Blog.title, Blog.description FROM Blog 
            LEFT JOIN DailyBlog ON (Blog.id = DailyBlog.blog_id AND DailyBlog.created_at > :date_limit) 
            JOIN BlogPost ON (BlogPost.blog_id = Blog.id) 
            WHERE 
                DailyBlog.id IS NULL AND
                Blog.approved = 1 AND
                CHAR_LENGTH(Blog.description) >40 AND
                BlogPost.created_at > :date_last_post
            ORDER BY RAND() 
            LIMIT 1', $rsm);
        $query->setParameter('date_limit', $date_limit_ignored->format('Y-m-d H:i:sP'));
        
        $date = new \DateTime();
        $date->modify('- 1 month');
        $query->setParameter('date_last_post', $date->format('Y-m-d H:i:sP'));
        
        return current($query->getResult());
    }
}