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
use Symfony\Component\HttpFoundation\RedirectResponse;

use Rizeway\BloginyBundle\Model\Utils\Contact;
use Rizeway\BloginyBundle\Form\ContactForm;
use Rizeway\BloginyBundle\Model\Mail\ContactMail;

class BloginyController extends Controller
{

    public function aboutAction()
    {
        return $this->render('BloginyBundle:Bloginy:about.html.twig');
    }
    
    public function faqAction()
    {
        return $this->render('BloginyBundle:Bloginy:faq.html.twig');
    }
    
    public function pluginsAction()
    {
        return $this->render('BloginyBundle:Bloginy:plugins.html.twig');
    }
    
    public function conditionsAction()
    {
        return $this->render('BloginyBundle:Bloginy:conditions.html.twig');
    }
    
    public function applicationsAction()
    {
        return $this->render('BloginyBundle:Bloginy:applications.html.twig');
    }
    
    public function promoteAction()
    {
        return $this->render('BloginyBundle:Bloginy:promote.html.twig');
    }
    
    public function contactAction()
    {
        $contact = new Contact();
        $form = $this->get('form.factory')->create(new ContactForm(), $contact);
        $saved = false;
        
        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
               // Send the contact mail
                $mail = new ContactMail(array('contact' => $contact));
                $mail->send($this->get('mailer'), $this->get('templating'));

                
                $this->get('session')->setFlash('notice', 'Your message has been successfully sent!');
                
                // Redirect
                return new RedirectResponse($this->generateUrl('bloginy_contact'));
            }
        }
        
        return $this->render('BloginyBundle:Bloginy:contact.html.twig', array('form' => $form->createView()));
    }
}
