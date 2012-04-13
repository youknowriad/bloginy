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
use Doctrine\ORM\EntityManager;

class BlogsTransform implements DataTransformerInterface
{
    protected $em;
    
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        $slugs = \explode(',', $value);
        $blogs = $this->em->getRepository('BloginyBundle:Blog')->findBySlugs($slugs);
        
        return $blogs;
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
        
        $slugs = array();
        foreach ($value as $blog)
        {
            $slugs[] = array('id' => $blog->getSlug(), 'label' => $blog->getShortTitle(40));
        }
        
        return json_encode($slugs);
    }
}