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

use Exercise\HTMLPurifierBundle\Form\HTMLPurifierTransformer;

class PostProposeForm extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('title', 'text', array('required' => false));
        $content_builder = null;
        if (!$options['write_post']) {
            $content_builder = $builder->create('content', 'textarea', array('required' => false));
            $builder->add('link', 'hidden');
        }
        else
        {
            $content_builder = $builder->create('content', 'rizeway_tinymce', array('required' => false));
        }
        $builder->add('category', 'entity', array(
            'required' => true, 
            'class'    => 'Rizeway\\BloginyBundle\\Entity\\Category'
            ));
        $builder->add('tags', 'rizeway_autocompleter', array(
            'required' => false,
            'value_transformer' => $options['tags_transformer'],
            'url' => $options['tag_url']));
        
        
        $purifier = new \HTMLPurifier($options['purifier_config']);
        $transformer = new HTMLPurifierTransformer($purifier);
        
        $content_builder->appendClientTransformer($transformer);
        $builder->add($content_builder);
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'category_choices' => array(),
            'data_class' => 'Rizeway\BloginyBundle\Entity\Post',
            'tag_url' => null,
            'tags_transformer' => null,
            'write_post' => false,
            'purifier_config' => null
        );
    }

    public function getName()
    {
        return 'post_propose';
    }
}