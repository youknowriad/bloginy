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

namespace Rizeway\BloginyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBloginyLanguagesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:update-languages')
             ->setDescription('Update the posts and blog posts language');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        \set_time_limit(0);
        
        $detector = $this->getContainer()->get('bloginy.language_detector');
        $languages = $this->getContainer()->getParameter('bloginy.post.language');
        
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        
        
        // Blog language
        $output->writeln('<info>Updating Blog Language...</info>');
        $momory_limit = \ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        
        $blogs = $em->getRepository('BloginyBundle:Blog')->findAll();
        $count = 0;
        foreach ($blogs as $blog)
        {
            
            $text = (strlen($blog->getDescription()) > 20 ) ? $blog->getDescription() : $blog->getTitle();
            $count++;
            $language = $detector->bingDetect($text);
            
            if (!isset($languages[$language])) {
                $language = 'other';
            }
            $blog->setLanguage($language);
            $output->writeln($blog->getTitle(). ' : ' .$blog->getLanguage());
            if ($count % 20 == 0) {
                $em->flush();
                $count = 1;
            }
        }
        $em->flush();
        $em->clear();
        ini_set('memory_limit', $momory_limit);
        
        // Post language
        $output->writeln('<info>Updating Post Language...</info>');
        $momory_limit = \ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        
        $posts = $em->getRepository('BloginyBundle:Post')->findBy(array('language' => 'other'));
        $count = 0;
        foreach ($posts as $post)
        {
            $text = (strlen($post->getResume()) > 20 ) ? $post->getResume() : $post->getTitle();
            $count++;
            $language = $detector->bingDetect($text);
            while (!$language) {
                $em->flush();
                \sleep(40);
                $language = $detector->bingDetect($text);
                $count = 1;
            }
            if (!isset($languages[$language])) {
                $language = 'other';
            }
            $post->setLanguage($language);
            $output->writeln($post->getTitle(). ' : ' .$post->getLanguage());
            if ($count % 20 == 0) {
                $em->flush();
                $count = 1;
            }
        }
        $em->flush();
        $em->clear();
        ini_set('memory_limit', $momory_limit);

        // Blog Post Language
        $output->writeln('<info>Updating Blog Post Language...</info>');
        $momory_limit = \ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        
        $posts = $em->getRepository('BloginyBundle:BlogPost')->findBy(array('language' => ''));
        $count = 0;
        foreach ($posts as $post)
        {
            
            $text = (strlen($post->getResume()) > 20 ) ? $post->getResume() : $post->getTitle();
            $count++;
            $language = $detector->bingDetect($text);
            while (!$language) {
                $em->flush();
                \sleep(40);
                $language = $detector->bingDetect($text);
                $count = 1;
            }
            if (!isset($languages[$language])) {
                $language = 'other';
            }
            $post->setLanguage($language);
            $output->writeln($post->getTitle(). ' : ' .$post->getLanguage());
            if ($count % 20 == 0) {
                $em->flush();
                $count = 1;
            }
        }
        
        $em->flush();
        $em->clear();
        ini_set('memory_limit', $momory_limit);
        
        return 0;
    }

}