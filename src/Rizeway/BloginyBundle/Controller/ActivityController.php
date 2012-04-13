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

use Rizeway\BloginyBundle\Model\Utils\Operators;

class ActivityController extends Controller
{
    public function liveAction($from = 'none', $refresh = false)
    {
        $em = $this->get('doctrine')->getEntityManager();

        $date = new \DateTime($from == 'none' ? null : $from);
        $activities = $em->getRepository('BloginyBundle:Activity')
            ->findFrom($date, $this->container->getParameter('bloginy.activity.max_results'),
                $refresh ? Operators::OPERATOR_GREATER_THAN : Operators::OPERATOR_LESS_THAN);
        $first_activity = (!\is_null($activities) && \count($activities))? \reset($activities) : null;
        $last_activity  = (!\is_null($activities) && \count($activities))? \end($activities) : null;

        $view = ($this->get('request')->isXmlHttpRequest()) ? 'BloginyBundle:Activity:live_ajax.html.twig': 'BloginyBundle:Activity:live.html.twig';

        return $this->render($view,
             array(
                 'refreshed' => true,
                 'refresh' => $refresh,
                 'activities' => $activities,
                 'show_pager' => (count($activities) == $this->container->getParameter('bloginy.activity.max_results')),
                 'from' => \is_null($last_activity) ? null : $last_activity->getCreatedAt()->format('Y-m-d H:i:sP'),
                 'refresh_from' => \is_null($first_activity) ? (($refresh) ? $from : null) : $first_activity->getCreatedAt()->format('Y-m-d H:i:sP')
             ));
    }
    
    public function lastAction()
    {
        $em = $this->get('doctrine')->getEntityManager();

        $activities = $em->getRepository('BloginyBundle:Activity')
            ->findFrom(new \DateTime(), $this->container->getParameter('bloginy.activity.sidebar.max_results'), Operators::OPERATOR_LESS_THAN);

        return $this->render('BloginyBundle:Activity:last.html.twig',
             array('activities' => $activities));
    }
}
