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

namespace Rizeway\BloginyBundle\Extension;

use Rizeway\BloginyBundle\Model\Utils\StringHandler;

class IsArabicExtension  extends \Twig_Extension {

    public function getFunctions() {
        return array(
            'is_arabic'  => new \Twig_Function_Method($this, 'isArabic'),
        );
    }

    public function isArabic($string) {
        $handler = new StringHandler();
        return $handler->isArabic($string);
    }

    public function getName()
    {
        return 'rizewat_twig_is_arabic_extension';
    }

}