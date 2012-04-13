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

class ContactForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name', 'text', array('required' => true, 'max_length' => 50));
        $builder->add('email', 'email', array('required' => true, 'max_length' => 100));
        $builder->add('subject', 'text', array('required' => true,'max_length' => 100));
        $builder->add('message', 'textarea', array('required' => true));
        $builder->add('captcha', 'captcha');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Rizeway\BloginyBundle\Model\Utils\Contact',
        );
    }

    public function getName()
    {
        return 'contact_form';
    }
}