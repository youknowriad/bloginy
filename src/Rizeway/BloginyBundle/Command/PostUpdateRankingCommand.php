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

class PostUpdateRankingCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:post:update-ranking')
             ->setDescription('Update the posts ranking');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $momory_limit = \ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        
        $output->writeln('<info>Updating posts\' ranking...</info>');
        $blog_posts_updater = $this->getContainer()->get('bloginy.post_ranking_updater');
        $blog_posts_updater->setOutputInterface($output);
        $blog_posts_updater->updatePosts();
        $output->writeln('<info>Updating posts\' ranking finished</info>');

        ini_set('memory_limit', $momory_limit);
        
        return 0;
    }

}