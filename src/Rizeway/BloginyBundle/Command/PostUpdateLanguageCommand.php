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

class PostUpdateLanguageCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:post:update-language')
             ->setDescription('Update post languages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Updating languages starting ...</info>');
        $posts = $this->getContainer()->get('doctrine')->getManager()
        	->getRepository('BloginyBundle:BlogPost')->findBy(array('approved' => 1));

        $language_detector = $this->getContainer()->get('bloginy.language_detector');
        $available_langugages = $this->getContainer()->getParameter('bloginy.post.language');

        foreach ($posts as $post) {
        	$text = (strlen($post->getResume()) > 20 ) ? $post->getResume() : $post->getTitle();
            
            if ($post->getLanguage() == '') {
	            $language = $language_detector->detect($text, $available_langugages);
	            if ($language !== 'other' && $language !== '' || $post->getLanguage() == '') {
	            	$post->setLanguage($language);
	            }
            }
        }
		$this->getContainer()->get('doctrine')->getManager()->flush();
        $output->writeln('<info>Language update finished</info>');

        return 0;
    }

}