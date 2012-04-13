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

namespace Rizeway\UserBundle\Tests\Model\Repository;

require_once __DIR__.'/../../../../../../app/AppKernel.php';

use Rizeway\UserBundle\Model\Repository\UserActivationRepository;

class UserActivationRepositoryTest extends \PHPUnit_Framework_TestCase
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

    public function testGetBaseQueryBuilder()
    {
        $repository = $this->em->getRepository('UserBundle:UserActivation');

        $this->assertEquals(get_class($repository), 'Rizeway\UserBundle\Model\Repository\UserActivationRepository');
        $this->assertEquals($repository->getBaseQueryBuilder()->getQuery()->getSql(),
              'SELECT u0_.code AS code0, u0_.count_tries AS count_tries1, u0_.locked AS locked2, u0_.created_at AS created_at3, u0_.id AS id4, u0_.user_id AS user_id5 FROM UserActivation u0_');

        return $repository;
    }

}
?>