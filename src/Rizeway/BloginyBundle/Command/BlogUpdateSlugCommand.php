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
use Rizeway\BloginyBundle\Model\Utils\SlugGenerator;

class BlogUpdateSlugCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:blog:update-slug')
             ->setDescription('Update blog slugs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Updating slugs starting ...</info>');
        $blogs = $this->getContainer()->get('doctrine')->getManager()
        	->getRepository('BloginyBundle:Blog')->findBy(array('approved' => 1));

        $slugGenerator = new SlugGenerator($this->getContainer()->get('doctrine')->getManager()->getRepository('BloginyBundle:Blog'));

        foreach ($blogs as $blog) {
            if ($slugGenerator->slugify($blog->getTitle()) != $blog->getSlug()) {
                $blog->setSlug($slugGenerator->generateUniqueSlug($blog->getTitle()));
            }
        }
		
        $this->getContainer()->get('doctrine')->getManager()->flush();
        $output->writeln('<info>Slugs update finished</info>');

        return 0;
    }

}