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

namespace Rizeway\BloginyBundle\Model\Utils;

require_once(__DIR__.'/../../../../../vendor/ArPHP/Arabic.php');

class StringHandler
{
    /**
     * Shorten a string
     * @param string $string
     * @param integer $max
     * @param string $trailing
     * @return string
     */
    public function shorten($string, $max = 100, $trailing='...')
    {
        if (\strlen($string) < $max)
		{
			return $string;
		}
		else
		{
			return \mb_substr($string, 0, $max - \strlen($trailing),'UTF-8').$trailing;
		}
    }

    /**
     * Sanitize a string
     * @param string $string
     * @param bool $strip_tags
     * @param bool $html_entities
     * @return string
     */
    public function sanitize($string, $strip_tags = true, $html_entities = true)
    {
        if ($strip_tags)
        {
            $string = \strip_tags($string);
        }

        return $string;
    }

    /**
     * Generate a radom string
     * @param int $length
     * @return string 
     */
    public function generateRandomString($length = 32)
    {
		$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q",
                                "r", "s", "t", "u", "v", "w", "x", "y", "z");
    	$code = '';
        for($i=0;$i<$length;$i++)
		{
			$code .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
		}

		return $code;
    }
    
    /**
     * 
     * @param type $string
     * @return type 
     */
	public function isArabic($string)
	{
		$Ar = new \Arabic('ArIdentifier');

		$pos = $Ar->identify($string);
		$total = count($pos);

		$countar = 0;
		// Compter le nombre de caractères en arabe
		for($i=0; $i<$total; $i+=2)
		{
			if (isset($pos[$i+1]))
			{
				$countar += $pos[$i+1] - $pos[$i];
			}
			else
			{
				$countar += strlen($string) - $pos[$i];
			}
		}

		if ($countar > strlen($string)/2)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
