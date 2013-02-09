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

namespace Rizeway\BloginyBundle\Model\Mail;

abstract class BaseMail {

    /**
     * @var string $subject
     */
    protected $subject = 'Bloginy Contacts';

    /**
     * @var string $from
     */
    protected $from = 'contact@bloginy.com';

    /**
     * @var string $to
     */
    protected $to = 'bloginy@rizeway.com';

    /*
     * @var string $content_type
     */
    protected $content_type = 'text/html';

    /**
     * @var $view_params
     */
    protected $view_params = array();

    /**
     *
     * @var string $view
     */
    protected $view;

    /**
     *
     * @param \Swift_Mailer $mailer
     * @param \Symfony\Bundle\FrameworkBundle\Templating\Engine $templating_engine
     */
    public function send($mailer, $templating_engine)
    {   
        $message = \Swift_Message::newInstance()
            ->setSubject($this->subject)
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setContentType($this->content_type)
            ->setBody($templating_engine->render($this->view, $this->view_params));

        return $mailer->send($message);
    }

    public function __construct($view_params)
    {
        $this->view_params = $view_params;
    }

}

?>
