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
use Rizeway\BloginyBundle\Model\Utils\CategoryDetector;

class UpdatePostCategoriesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:post:update-categories')
             ->setDescription('Update the posts categories');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        \set_time_limit(0);
        
        $detector = new CategoryDetector($this->getContainer()->getParameter('alchemy_api_key'));
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        
        // Post language
        $output->writeln('<info>Updating Post Categories...</info>');
        $momory_limit = \ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        
        $posts = $em->getRepository('BloginyBundle:Post')->findBy(array('category' => null));
        $count = 0;
        foreach ($posts as $post)
        {
            $count++;
            $text = strlen(trim($post->getResume())) > 20 ? $post->getResume() : (strlen(trim($post->getTitle())) > 10 ? $post->getTitle() : null);
            $category_name = is_null($text) ? 'Other' : $detector->detect($text);
            $category = $em->getRepository('BloginyBundle:Category')->findOneBy(array('name' => $category_name));
            $post->setCategory($category);
            $output->writeln($post->getTitle(). ' : ' .$category_name);
            if ($count % 20 == 0) {
                $em->flush();
            }
        }
        
        $em->flush();
        $em->clear();
        ini_set('memory_limit', $momory_limit);
        
        return 0;
    }

}