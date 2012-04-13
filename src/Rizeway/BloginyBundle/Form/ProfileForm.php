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
use Rizeway\BloginyBundle\Model\Utils\Language;

class ProfileForm extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('last_name', 'text', array('required' => false, 'max_length' => 50));
        $builder->add('first_name', 'text', array('required' => false, 'max_length' => 100));
        $builder->add('birthday', 'birthday', array('required' => false, 'pattern' => '{{ day }}-{{ month }}-{{ year }}'));
        $builder->add('web_site', 'url', array('required' => false, 'max_length' => 100));
        
        $language = new Language();
        $builder->add('locale', 'choice', array('choices' => $language->getLocaleChoices()));
        
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Rizeway\UserBundle\Entity\User',
        );
    }

    public function getName()
    {
        return 'edit_profile';
    }
}