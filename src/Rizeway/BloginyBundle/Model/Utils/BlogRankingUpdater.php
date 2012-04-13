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
use Symfony\Component\Console\Output\OutputInterface;

use Rizeway\BloginyBundle\Entity\Blog;

class BlogRankingUpdater
{
    /**
     * Doctrine Entity Manager
     *
     * @var EntityManager
     */
    private $entity_manager;

    /**
     *
     * @var OutputInterface
     */
    private $output;

    public function __construct(EntityManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    public function setOutputInterface(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Update the blogs ranking retrieved using the query builder
     *
     * @param QueryBuilder $qb
     */
    public function updateBlogs(QueryBuilder $qb = null)
    {
        $limit = new \DateTime();
        $limit->modify('- 75 days');
        $limit->setDate($limit->format('Y'), $limit->format('m'), 1);
        $limit->setTime(0, 0, 0);
        
        $blogs = $this->entity_manager->getRepository('BloginyBundle:Blog')
                ->findForQueryBuilder(1, null, $qb);

        $this->log(sprintf('<info>%s Blogs to update</info>', count($blogs)));

        foreach ($blogs as $blog)
        {
          $this->updateBlog($blog, $limit);
        }

        $this->entity_manager->flush();
        $this->log('<info>Rankings Saved</info>');
    }

    /**
     * Update a blog ranking
     * 
     * @param Blog $blog
     * @param \DateTime $limit
     */
    public function updateBlog(Blog $blog, \DateTime $limit)
    {
        $this->log(sprintf('<info>Updating Blog : %s (%s)</info>', $blog->getTitle(), $blog->getUrl()));

        $last_blog_posts = $this->entity_manager->getRepository('BloginyBundle:BlogPost')
            ->findProposedPostsForBlogUntil($blog, $limit);

        $count = 0;
        $count2 = 0;
        $count3 = 0;

        $popularite = 0;
        $popularite2 = 0;
        $popularite3 = 0;

        //dates
        $month_3 = $limit;

        $month_2 = clone $month_3;
        $month_2->modify('+ 1 month');

        $month_1 = clone $month_2;
        $month_1->modify('+ 1 month');

        $month_0 = clone $month_1;
        $month_0->modify('+ 1 month');

        foreach ($last_blog_posts as $blog_post)
        {
            $post = $blog_post->getPost();

            if ($post->getCreatedAt()->getTimestamp() >= $month_3->getTimestamp() && $post->getCreatedAt()->getTimestamp() < $month_2->getTimestamp())
            {
              $count++;
              $popularite += $post->getBlogRankingValue();
            }
            elseif ($post->getCreatedAt()->getTimestamp() >= $month_2->getTimestamp() && $post->getCreatedAt()->getTimestamp() < $month_1->getTimestamp())
            {
              $count2++;
              $popularite2 += $post->getBlogRankingValue();
            }
            elseif ($post->getCreatedAt()->getTimestamp() >= $month_1->getTimestamp() && $post->getCreatedAt()->getTimestamp() < $month_0->getTimestamp())
            {
              $count3++;
              $popularite3 += $post->getBlogRankingValue();
            }
        }

        $popularite  = ($count  == 0)? 0 : (int)($popularite*1000/$count);
        $popularite2 = ($count2 == 0)? 0 : (int)($popularite2*1000/$count2);
        $popularite3 = ($count3 == 0)? 0 : (int)($popularite3*1000/$count3);

        $blog->setRankValue($popularite+$popularite2+$popularite3);

        $this->log(sprintf('<info>Blog Rank Value updated : %s</info>', $blog->getRankValue()));
    }

    protected function log($message)
    {
        if (!\is_null($this->output))
        {
            $this->output->write($message, true);
        }
    }

}