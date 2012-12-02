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

namespace Rizeway\BloginyBundle\Model\Feed;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ContainerAwareFeedWriter {
    
    static $types = array('rss2', 'atom');
    
    /**
     * The Zend Writer
     * 
     * @var \ezcFeed
     */
    protected $writer;
   
    protected $type = 'rss2';
    
    /**
     *
     * @var ContainerInterface
     */
    protected $container;


    /**
     *
     * @param ContainerInterface $container
     * @param type $options 
     * 
     *   - title
     *   - link
     *   - feed_link
     *   - description
     *   - author_name
     *   - author_email
     *   - author_web_site
     */
    public function __construct(ContainerInterface $container, $type ='rss2', $options = array())
    {
        if (! \in_array($type, self::$types))
        {
            throw new \Exception('This feed type is not supported : '.$type);
        }

        $this->container = $container;
        
        $this->writer  = new \ezcFeed();
        $this->writer->title = isset($options['title']) ? $options['title'] : $container->getParameter('bloginy.title');
        $this->writer->description = isset($options['description']) ? $options['description'] : $container->getParameter('bloginy.description');
        $this->writer->published = new \DateTime();
        $this->writer->updated = new \DateTime();

        $author = $this->writer->add('author');
        $author->name = isset($options['author_name']) ? $options['author_name'] : $container->getParameter('bloginy.title');
        $author->email = isset($options['author_email']) ? $options['author_email'] : $container->getParameter('bloginy.email');

        $url = isset($options['link']) ? $options['link'] : $this->container->get('router')->generate('homepage', array(), true);
        $this->writer->id = $url;
        $link = $this->writer->add( 'link' );
        $link->href = $url;
        
        $this->type = $type;
    }
    
    public function generate()
    {
        return $this->writer->generate($this->type);
    }
}