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
use Symfony\Component\HttpFoundation\Response;
use Rizeway\BloginyBundle\Model\Utils\StringHandler;

class TagController extends Controller
{
    public function autocompleteAction()
    {
        $request = $this->getRequest();
        $value = $request->get('term');
        $limit = $request->get('limit', 10);
        $tags = $this->getDoctrine()->getRepository('BloginyBundle:Tag')->filterByTag($value, $limit);
        $handler = new StringHandler();
        
        $results = array();
        foreach($tags as $tag) {
            $results[] = array('id' => $tag, 'label' => $handler->shorten($tag, 35));
        }

        $response = new Response();
        $response->setCharset('application/json');
        $response->setContent(json_encode($results));

        return $response;
    }
}
