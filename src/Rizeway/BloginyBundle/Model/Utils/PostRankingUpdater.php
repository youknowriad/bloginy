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

use Rizeway\BloginyBundle\Entity\Post;

class PostRankingUpdater
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
    public function updatePosts(QueryBuilder $qb = null)
    {

        $posts = $this->entity_manager->getRepository('BloginyBundle:Post')->findToRank();

        $this->log(sprintf('<info>%s Posts to update</info>', count($posts)));

        $count = 0;
        foreach ($posts as $post)
        {
          $count++;
          $this->updatePost($post); 
        }
        
        $this->entity_manager->flush();
        $this->entity_manager->clear();
        
        $this->log('<info>Rankings Saved</info>');
    }

    /**
     * Update a blog ranking
     *
     * @param Post $post
     */
    public function updatePost(Post $post)
    {
        $this->log(sprintf('<info>Updating Post : %s</info>', $post->getTitle()));

        $time_points = (int) ((\time() - $post->getCreatedAt()->getTimestamp()) / 3600);
        $time_points = ($time_points < 8360) ? $time_points : 8360 ;
        $total_points =  $time_points - 12 * ($post->getCountVotes());

        $post->setRankValue($total_points);

        $this->log(sprintf('<info>Post Rank Value updated : %s</info>', $post->getRankValue()));
    }

    protected function log($message)
    {
        if (!\is_null($this->output))
        {
            $this->output->write($message, true);
        }
    }

}