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

use Rizeway\BloginyBundle\Model\Utils\SlugGenerator;

class SlugGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DoctrineORMEntityManager
     */
    protected $em;

    public function __construct()
    {
        $kernel = new \AppKernel('dev', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testSlugify()
    {
        $slugGenerator = new SlugGenerator($this->em->getRepository('Rizeway\BloginyBundle\Entity\Blog'));

        $this->assertEquals($slugGenerator->slugify('Article par défaut'), 'article-par-d-faut');
        $this->assertEquals($slugGenerator->slugify('M3ak Ya Mourad Meghni معاك يا مراد'), 'm3ak-ya-mourad-meghni-معاك-يا-مراد');

        return $slugGenerator;
    }

    /**
     * @depends testSlugify
     */
    public function testGenerateUniqueSlug(SlugGenerator $slugGenerator)
    {
        /*$this->assertEquals($slugGenerator->generateUniqueSlug('Article par défaut'), '');
        $this->assertEquals($slugGenerator->generateUniqueSlug('youknowriad'), '');*/
    }
}
?>