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

class CategoryDetector
{

    private $api_key;

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }
    
    private static $categories = array(
        'arts_entertainment'   => 'Entertainment',
        'business'             => 'Economy',
        'computer_internet'    => 'Technology',
        'culture_politics'     => 'Politics',
        'gaming'               => 'Entertainment',
        'health'               => 'Health',
        'law_crime'            => 'Other',
        'religion'             => 'Religion',
        'recreation'           => 'Entertainment',
        'science_technology'   => 'Science',
        'sports'               => 'Sports',
        'weather'              => 'Other',
        'unknown'              => 'Other'
    );
    
    public function detect($text)
    {
        $alchemyObj = new \AlchemyAPI();
        $alchemyObj->setAPIKey($this->api_key);
        
        try {
            $result = \json_decode($alchemyObj->TextGetCategory($text, \AlchemyAPI::JSON_OUTPUT_MODE), true);
            if (isset($result['category']) && ($result['score'] > 0.4)) {
                return $this->getCorrespondinBloginyCategory($result['category']);
            }
        } catch (\Exception $e) {}
        
        return 'Other';
    }
    
    private function getCorrespondinBloginyCategory($category)
    {
        if (isset(self::$categories[$category])) {
            return self::$categories[$category];
        }
        
        return 'Other';
    }
}
