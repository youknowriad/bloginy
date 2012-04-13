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

namespace Rizeway\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Rizeway\UserBundle\Model\Login;
use Rizeway\UserBundle\Entity\User;
use Rizeway\UserBundle\Entity\UserActivation;
use Rizeway\UserBundle\Form\UserSubscribeForm;
use Rizeway\UserBundle\Model\Mail\ActivationMail;

use Rizeway\BloginyBundle\Model\Factory\ActivityFactory;

class UserController extends Controller
{
    public function subscribeAction()
    {
        $user = new User();
        $form = $this->get('form.factory')->createNamed(new UserSubscribeForm(), 'registration', $user);

        if ('POST' === $this->get('request')->getMethod()) {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {

                // Save the user and the activation
                $user_activation = new UserActivation();
                $user_activation->setUser($user);        
                $user->setLocale($this->get('request')->getSession()->getLocale());
                $this->get('doctrine')->getEntityManager()->persist($user);
                $this->get('doctrine')->getEntityManager()->persist($user_activation);
                $this->get('doctrine')->getEntityManager()->flush();

                // Send the activation mail
                $mail = new ActivationMail($user->getEmail(), array('user_activation' => $user_activation));
                $mail->send($this->get('mailer'), $this->get('templating'));

                // Redirect
                return new RedirectResponse($this->generateUrl('user_subscribed', array('username' => $user->getUsername())));
            }
        }

        return $this->render('UserBundle:User:subscribe.html.twig',
                array('form' => $form->createView()));
    }

    public function subscribedAction($username)
    {
        $user = $this->get('doctrine')->getEntityManager()
                ->getRepository('UserBundle:User')
                ->findOneBy(array('username'=>$username));

        if (is_null($user))
        {
          throw new \Exception(sprintf('the user %s was not found', $username));
        }

        return $this->render('UserBundle:User:subscribed.html.twig',
                array('user' => $user));
    }

    public function activateAction($username, $code)
    {
        $em = $this->get('doctrine')->getEntityManager();
        $user = $em->getRepository('UserBundle:User')
                ->findOneBy(array('username'=>$username));

        if (is_null($user)) {
            throw new \Exception(sprintf('the user %s was not found', $username));
        }

        if ($user->getApproved()) {
            return $this->render('UserBundle:User:approved.html.twig');
        }

        $activation = $em->getRepository('UserBundle:UserActivation')
                ->findOneByUser($user);

        if (is_null($activation)) {
            throw new \Exception(sprintf('the activation request was not found'));
        }
        
        if ($activation->getLocked()) {
            return $this->render('UserBundle:User:locked.html.twig');
        }

        if ($activation->getCode() != $code) {
            $activation->setCountTries($activation->getCountTries() + 1);
            if ($activation->getCountTries() > $this->container->getParameter('user.activation.max_tries')) {
                $activation->setLocked(true);
            }
            $em->flush();

            throw new \Exception('Your activation code is invalid');
        }

        $user->setApproved(true);
        $activity_factory = new ActivityFactory();
        $activity = $activity_factory->buildForUserCreation($user);
        
        $em->persist($activity);
        $em->remove($activation);
        $em->flush();
        
        return $this->render('UserBundle:User:activated.html.twig');
    }

    public function loginAction()
    {
        // In case of ajax request 
        if ($this->get('request')->isXmlHttpRequest())
        {
            $this->get('request')->getSession()->remove('_security.target_path');
            return $this->render('UserBundle:User:ajax_login_redirect.html.twig');
        }
        
        // get the error if any (works with forward and redirect -- see below)
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('UserBundle:User:login.html.twig', array(
            // last username entered by the user
            'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
            'error'         => $error
        ));
    }

    public function loggedOutAction()
    {
        return $this->render('UserBundle:User:logged_out.html.twig');
    }
    
    public function loginPanelAction()
    {
        $user = new User();
        $form = $this->get('form.factory')->createNamed(new UserSubscribeForm(), 'registration', $user);

        return $this->render('UserBundle:User:login_panel.html.twig',
                array('form' => $form->createView()));
    }
}
