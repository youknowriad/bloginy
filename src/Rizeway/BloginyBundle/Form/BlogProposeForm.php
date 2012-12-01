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

namespace Rizeway\BloginyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class BlogProposeForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text', array('required' => true, 'max_length' => 255));
        $builder->add('description', 'textarea');
        $builder->add('url', 'url', array('required' => true, 'max_length' => 255));
        $builder->add('feed_url', 'url', array('required' => true, 'max_length' => 255));
        $builder->add('language', 'choice', array('choices' => $options['language_choices']));
        $builder->add('location', 'choice', array('choices' => $options['location_choices']));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'location_choices' => array(),
            'language_choices' => array(),
            'data_class' => 'Rizeway\BloginyBundle\Entity\Blog',
        );
    }

    public function getName()
    {
        return 'blog_propose';
    }
}