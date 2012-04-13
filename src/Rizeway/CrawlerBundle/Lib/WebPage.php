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

namespace Rizeway\CrawlerBundle\Lib;

class WebPage {
    

    private $url;
    
    private $title;
    
    private $description;
    
    /**
     *
     * @param string $url The address of the web page to parse 
     */
    public function __construct($url)
    {
        $this->url = $url;
        
        $this->parse();
    }
    
    /**
     * Get The parsed url
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Get the title of the parsed url
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Get the description of the parsed url
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
    
    
    private function parse()
    {   
        if (!($contents = $this->getUrlContents($this->url)))  
        {
            throw new \Exception('Unable to access the requested page : '.$this->url);
        }

        // Getting the title
        preg_match('/<title>([^>]*)<\/title>/si', $contents, $match );
        if (isset($match) && is_array($match) && count($match)) {
            $this->title = \html_entity_decode(strip_tags(current($match)), ENT_QUOTES);
        }
        
        // Getting the met tags
        $metaTags = null;
        preg_match_all('/<[\s]*meta[\s]*name="?([^>"]*)"?[\s]*content="?([^>"]*)"?[\s]*[\/]?[\s]*>/si', $contents, $match);
        if (isset($match) && is_array($match) && count($match) == 3)
        {
            $htmls = $match[0];
            $names = $match[1];
            $values = $match[2];

            if (count($htmls) == count($names) && count($names) == count($values))
            {
                $metaTags = array();
                for ($i=0, count($names); $i < count($names); $i++)
                {
                    $metaTags[$names[$i]] = array (
                        'html' => htmlentities($htmls[$i]),
                        'value' => $values[$i]
                    );
                }
            }
        }
        
        $this->description = isset($metaTags['description']) ? \html_entity_decode($metaTags['description']['value'], ENT_QUOTES): '';
    }
    
    
    private function getUrlContents($url, $maximumRedirections = 3, $currentRedirection = 0)
    {
        $result = false;
        $contents = @file_get_contents($url);

        if (isset($contents) && is_string($contents))
        {
            preg_match_all('/<[\s]*meta[\s]*http-equiv="?REFRESH"?' . '[\s]*content="?[0-9]*;[\s]*URL[\s]*=[\s]*([^>"]*)"?' . '[\s]*[\/]?[\s]*>/si', $contents, $match);

            if (isset($match) && is_array($match) && count($match) == 2 && count($match[1]) == 1)
            {
                if (!isset($maximumRedirections) || $currentRedirection < $maximumRedirections)
                {
                    return getUrlContents($match[1][0], $maximumRedirections, ++$currentRedirection);
                }

                $result = false;
            }
            else
            {
                $result = $contents;
            }
        }

        return $result;
    }
}
