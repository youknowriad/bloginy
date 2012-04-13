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
use Symfony\Component\Form\FormBuilder;

class NewPageForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => false, 'max_length' => 100));
        $builder->add('public', 'checkbox');
        $builder->add('tags', 'rizeway_autocompleter', array(
            'required' => false,
            'value_transformer' => $options['tags_transformer'],
            'url' => $options['tag_url']));
        $builder->add('blogs', 'rizeway_autocompleter', array(
            'required' => false,
            'value_transformer' => $options['blogs_transformer'],
            'url' => $options['blog_url'],
            'must_match' => true
        ));
        $builder->add('users', 'rizeway_autocompleter', array(
            'required' => false,
            'value_transformer' => $options['users_transformer'],
            'url' => $options['user_url'],
            'must_match' => true
         ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Rizeway\BloginyBundle\Entity\Page',
            'tag_url' => null,
            'tags_transformer' => null,
            'blog_url' => null,
            'blogs_transformer' => null,
            'user_url' => null,
            'users_transformer' => null
        );
    }

    public function getName()
    {
        return 'new_page';
    }
}