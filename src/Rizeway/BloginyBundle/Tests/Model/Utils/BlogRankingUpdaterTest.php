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

namespace Rizeway\BloginyBundle\Tests\Model\Utils;

require_once __DIR__.'/../../../../../../app/AppKernel.php';

use Rizeway\BloginyBundle\Model\Utils\BlogRankingUpdater;

class BlogRankingUpdaterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineORMEntityManager
     */
    protected $em;

    public function __construct()
    {
        $kernel = new \AppKernel('dev', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine')->getEntityManager();
        date_default_timezone_set('Europe/Paris');
    }


    public function testUpdateBlog()
    {
        $updater = new BlogRankingUpdater($this->em);
        $blog = $this->em->getRepository('Rizeway\BloginyBundle\Entity\Blog')->findOneBy(array('slug' => 'youknowriad'));
        $limit = new \DateTime();
        $limit->modify('- 60 days');
        $limit->setDate($limit->format('Y'), $limit->format('m'), 1);
        $limit->setTime(0, 0, 0);

        $updater->updateBlog($blog, $limit);

        $this->assertEquals(5000, $blog->getRankValue());
    }
}
?>