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

use Rizeway\BloginyBundle\Model\Utils\LanguageDetector;

class LanguageDetectorTest extends \PHPUnit_Framework_TestCase
{
    public function testDetect()
    {
        $detector = new LanguageDetector('', '');

        $this->assertEquals($detector->detect('Phrase en français'), 'fr');
        $this->assertEquals($detector->detect('English sentence'), 'en');
        $this->assertEquals($detector->detect('جملة عربية '), 'ar');
        $this->assertEquals($detector->detect('جملة عربية ', array('fr', 'en')), 'other');
    }
}
?>