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

class BlogUpdateRankingCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:blog:update-ranking')
             ->setDescription('Update the blogs ranking');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln('<info>Updating blogs\' ranking...</info>');
        $blog_posts_updater = $this->getContainer()->get('bloginy.blog_ranking_updater');
        $blog_posts_updater->setOutputInterface($output);
        $blog_posts_updater->updateBlogs();
        $output->writeln('<info>Updating blogs\' ranking finished</info>');

        return 0;
    }

}