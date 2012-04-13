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

namespace Rizeway\BloginyBundle\Form\DataTransform;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\Request;

use Rizeway\BloginyBundle\Entity\PageHasTag;
use Rizeway\BloginyBundle\Entity\Page;

class PageTagsTransform implements DataTransformerInterface
{
    protected $page;
    
    public function __construct(Page $page)
    {
        $this->page = $page;
    }
    
    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $tags = array();
        $values = \explode(',', $value);
        foreach ($values as $val)
        {
            if (\trim($val) !== '')
            {
                $tag = new PageHasTag();
                $tag->setTag(\trim($val));
                $tag->setPage($this->page);  
                
                $tags[] = $tag;
            }
        }
        
        return $tags;
    }
    
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (\is_null($value))
        {
            return '[]';
        }
        
        $tags = array();
        foreach ($value as $tag)
        {
            $tags[] = array('id' => $tag->getTag(), 'label' => $tag->getShortTag(40));
        }
        
        return json_encode($tags);
    }
}