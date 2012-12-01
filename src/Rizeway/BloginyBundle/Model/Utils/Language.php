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

class Language
{
    protected $language = 'fr_FR';

    public function getLanguage()
    {
        return $this->language;
    }

    public function getLocale()
    {
        $languages = $this->getAvailableLanguages();
        
        return $languages[$this->language]['locale'];
    }

    public function setLanguage($language)
    {
        $language = \substr($language, 0, 2);
        $languages = $this->getAvailableLanguages();
        if (isset($languages[$language]))
        {
            $this->language = $language;
        }
    }
    
    public function getAvailableLanguages()
    {
        return array(
            'ar' => array('locale' => 'ar_DZ', 'label' => 'Arabic'),
            'fr'=> array('locale' => 'fr_FR' ,'label' => 'French'),
            'en'=> array('locale' => 'en_US', 'label' => 'English'));
    }
    
    public function getLocaleChoices() {
        $array  = array();
        foreach ($this->getAvailableLanguages() as $lang)
        {
            $array[$lang['locale']] = $lang['label'];
        }
        
        return $array;
    }
}
