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

namespace Rizeway\BloginyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Rizeway\BloginyBundle\Model\Utils\Language;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class UtilsController extends Controller
{
    public function switchLanguageAction($language = null)
    {
        // Get the referer
        $refer = $this->get('request')->getUri();
       
        // Get the referer
        $lang = new Language();
        $lang->setLanguage(is_null($language)? $this->get('request')->getSession()->getLocale():$language);
        if (!is_null($language))
        {
            $this->get('request')->getSession()->setLocale($lang->getLocale());
        }

        if ($this->get('request')->get('refer'))
        {
           return  new RedirectResponse($this->get('request')->get('refer'));
        }

        return $this->render('BloginyBundle:Utils:switch_language.html.twig',
            array(
                'language' => $lang->getLanguage(),
                'refer' => $refer,
                'availableLanguages' => $lang->getAvailableLanguages()));        
    }

    public function languageClassAction()
    {
        $class = sprintf('class="%s"', $this->get('request')->getSession()->getLocale());
        $direction = 'dir="'. (($this->get('request')->getSession()->getLocale() == 'ar_DZ') ? 'rtl' : 'ltr') .'"';
        return new Response($class.' '.$direction);
    }

    public function mainMenuAction($current = null)
    {
        return $this->render('BloginyBundle:Utils:main_menu.html.twig', array('current' => $current));
    }
}
