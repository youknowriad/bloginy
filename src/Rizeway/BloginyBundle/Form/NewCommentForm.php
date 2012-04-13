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

class NewCommentForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        if (!$options['authenticated'])
        {
            $builder->add('username', 'text', array('required' => true, 'max_length' => 100));
            $builder->add('web_site', 'url', array('required' => false, 'max_length' => 100));
            $builder->add('email'   , 'email', array('required' => true, 'max_length' => 100));
            $builder->add('captcha', 'captcha');
        }

        $comment_builder = $builder->create('comment', 'textarea');
        
        
        $purifier = new \HTMLPurifier($options['purifier_config']);
        $transformer = new HTMLPurifierTransformer($purifier);
        
        $comment_builder->appendClientTransformer($transformer);
        $builder->add($comment_builder);
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'authenticated' => false,
            'purifier_config' => null,
            'data_class' => 'Rizeway\BloginyBundle\Entity\Comment',
        );
    }

    public function getName()
    {
        return 'new_comment';
    }
}