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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;

class InitCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:init')
             ->setDescription('Initialize a bloginy project')
             ->addOption('load_test_data', 'l', InputOption::VALUE_NONE, 'Load Data Test');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $output->writeln('<info>Initializing ...</info>');
        
        // Clearing Cache
        $command = $this->getApplication()->find('cache:clear');
        $command->run(new ArrayInput(array('command'=> 'clearing cache')), $output);
        
        // Droping Actual Schema
        $command = $this->getApplication()->find('doctrine:schema:drop');
        $command->run(new ArrayInput(array('--force' => true, 'command'=> 'droping schema')), $output);

        // Doctrine Creating Schema
        $command = $this->getApplication()->find('doctrine:schema:create');
        $command->run(new ArrayInput(array('command' => 'creating new schema')), $output);
        
        // Fixtures
        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $command->run(new ArrayInput(array('command' => 'loading data test')), $output);  
        
        // Data Test
        if ($input->getOption('load_test_data'))
        {
            $command = $this->getApplication()->find('doctrine:fixtures:load');
            $command->run(new ArrayInput(array('command' => 'loading data test', '--append' => true, '--fixtures' => __DIR__.'/../DataTest/')), $output);  
        }
        
        // Clearing Cache
        $command = $this->getApplication()->find('cache:clear');
        $command->run(new ArrayInput(array('command'=> 'clearing cache')), $output);
        
        $output->writeln('<info>Init finished</info>');

        return 0;
    }

}