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

use Rizeway\BloginyBundle\Model\Utils\DailyBlogUpdater;

class UpdateDailyBlogCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:blog:update-daily-blog')
             ->setDescription('Update the daily blog');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln('<info>Refreshing the daily blog ...</info>');
        $updater = new DailyBlogUpdater($this->getContainer()->get('doctrine')->getEntityManager());
        $blog = $updater->updateDailyBlog();
        if ($blog)
        {
          $output->writeln('<info>The new daily blog is : '.$blog->getTitle().'</info>');  
        }

        return 0;
    }

}