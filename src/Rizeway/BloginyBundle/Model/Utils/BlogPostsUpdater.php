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
use Rizeway\BloginyBundle\Model\Utils\SlugGenerator;

class BlogPostsUpdater
{
    /**
     * Doctrine Entity Manager
     * 
     * @var EntityManager
     */
    private $entity_manager;

    /**
     * Feed Parser
     *
     * @var FeedParser
     */
    private $feed_parser;
    
    /**
     * Langugage Detector
     *
     * @var LanguageDetector
     */
    private $language_detector;

    /**
     * Available Languages
     *
     * @var array
     */
    private $available_langugages;

    /**
     * @var SlugGenerator
     */
    private $slug_generator;

    /**
     *
     * @var OutputInterface
     */
    private $output;

    public function __construct(EntityManager $entity_manager, $feed_parser, $language_detector, $available_languages)
    {
        $this->entity_manager = $entity_manager;
        $this->feed_parser = $feed_parser;
        $this->language_detector = $language_detector;
        $this->available_langugages = \array_keys($available_languages);
    }

    public function setOutputInterface(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function updateBlogs(QueryBuilder $qb = null)
    {
        $updateDate = new \DateTime();
        $updateDate->modify('- 30 minutes');
        $blogs = $this->entity_manager->getRepository('BloginyBundle:Blog')
                ->findReadyToUpdate($updateDate, $qb);

        $this->log(sprintf('<info>%s Blogs to update</info>', count($blogs)));

        foreach ($blogs as $blog)
        {
            $this->log(sprintf('<info>Updating Blog : %s (%s)</info>', $blog->getTitle(), $blog->getUrl()));
            $count = $this->updateBlog($blog);
            $this->log(sprintf('<info>Blog Posts Added : %s</info>', $count));
        }

        $this->entity_manager->flush();
        $this->entity_manager->clear();
    }

    public function updateBlog(Blog $blog)
    {
        $posts = $this->feed_parser->parse($blog->getFeedUrl());
        $count = 0;
        foreach ($posts as $post)
        {
           if (\is_null($blog->getPostsUpdatedAt()) || $blog->getPostsUpdatedAt() < $post->getPublishedAt() )
           {
               $text = (strlen($post->getResume()) > 20 ) ? $post : $post->getTitle();
               $post->setLanguage($this->language_detector->detect($text, $this->available_langugages));
               $post->setBlog($blog);
               $post->setSlug($this->getSlugGenerator()->generateUniqueSlug($post->getTitle()));
               $this->entity_manager->persist($post);
               $count++;
           }
        }
        $blog->setPostsUpdatedAt(new \DateTime());

        return $count;
    }

    protected function log($message)
    {
        if (!\is_null($this->output))
        {
            $this->output->write($message, true);
        }
    }

    protected function getSlugGenerator()
    {
        if (is_null($this->slug_generator))
        {
            $this->slug_generator = new SlugGenerator($this->entity_manager
                ->getRepository('BloginyBundle:BlogPost'));
        }

        return $this->slug_generator;
    }

}