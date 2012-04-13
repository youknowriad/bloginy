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

use Doctrine\ORM\EntityRepository;

class SlugGenerator
{
    /**
     *
     * @var EntityReposiotry
     */
    private $repository;

    /**
     *
     * @var string
     */
    private $field;

    /**
     * Cache of recently generated slugs
     *
     * @var array
     */
    private $last_generated_slugs = array();

    /**
     *
     * @param EntityRepository $repository
     * @param string $field
     */
    public function __construct(EntityRepository $repository, $field = 'slug')
    {
        $this->repository = $repository;
        $this->field = $field;
    }

    public function generateUniqueSlug($input)
    {
        $slug = $base_slug = $this->slugify($input);
        $i = 2;
        while ($this->repository->findOneBy(array($this->field => $slug)) || isset($this->last_generated_slugs[$slug]))
        {
            $slug = $base_slug.'-'.$i;
            $i++;
        }
        $this->last_generated_slugs[$slug] = 1;

        return $slug;
    }

    /**
     * Generate a Slug From a string
     *
     * @param string $input
     * @return string
     */
    public function slugify($input)
    {
        $input = \preg_replace('/[^a-zA-Z0-9\x{0600}-\x{06FF}]/u', '-', $input);
        $input = \mb_strtolower(\preg_replace('/-{2,}/', '-', \trim($input, '-')), 'UTF-8');
        if (\strlen($input) > 200) {
            $input = \substr($input, 0, 200);
        }
        
        return $input;
    }

    /**
     * Clean cache of last generated slugs
     */
    public function clean()
    {
        $this->last_generated_slugs = array();
    }
}
