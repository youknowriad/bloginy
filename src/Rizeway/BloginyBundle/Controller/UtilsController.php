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
        if (!is_null($language)) {
            $this->get('session')->set('_locale', $language);

            return  new RedirectResponse($this->get('request')->get('refer'));
        } elseif (is_null($this->get('session')->get('_locale'))) {
            $lang = new Language();
            $locale = $this->getRequest()->getPreferredLanguage(array_keys($lang->getAvailableLanguages()));
            $this->get('session')->set('_locale', $locale);
            $lang->setLanguage($locale);
        } else {
            $lang = new Language();
            $lang->setLanguage($this->get('session')->get('_locale'));
        }

        return $this->render('BloginyBundle:Utils:switch_language.html.twig',
            array(
                'language' => $lang->getLanguage(),
                'refer' => $this->get('request')->getUri(),
                'availableLanguages' => $lang->getAvailableLanguages()
            ));
    }

    public function languageClassAction()
    {
        $class = sprintf('class="%s"', $this->get('session')->get('_locale'));
        $direction = 'dir="'. (($this->get('session')->get('_locale') == 'ar') ? 'rtl' : 'ltr') .'"';
        return new Response($class.' '.$direction);
    }

    public function mainMenuAction($current = null)
    {
        return $this->render('BloginyBundle:Utils:main_menu.html.twig', array('current' => $current));
    }

    public function  switchLocaleAction()
    {
        $lang = new Language();
        return $this->render('BloginyBundle:Utils:_switch_locale.html.twig',
            array(
                'availableLanguages' => $lang->getAvailableLanguages(),
                'refer' => $this->getRequest()->getUri()
            ));
    }
}
