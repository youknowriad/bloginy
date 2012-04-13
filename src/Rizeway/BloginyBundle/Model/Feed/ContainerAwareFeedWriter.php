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
    
    static $types = array('rss', 'atom');
    
    /**
     * The Zend Writer
     * 
     * @var \Zend_Feed_Writer_Feed
     */
    protected $writer;
   
    protected $type = 'rss';
    
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
    public function __construct(ContainerInterface $container, $type ='rss', $options = array())
    {
        if (! \in_array($type, self::$types))
        {
            throw new \Exception('This feed type is not supported : '.$type);
        }
        
        $this->writer = new \Zend_Feed_Writer_Feed();
        $this->writer->setTitle(isset($options['title']) ? $options['title'] : $container->getParameter('bloginy.title'));
        $this->writer->setLink(isset($options['link']) ? $options['link'] : $container->getParameter('bloginy.link'));
        $this->writer->setFeedLink(isset($options['feed_link']) ? $options['feed_link'] : $container->getParameter('bloginy.feed_link.'.$type), $type);
        $this->writer->setDateModified(\time());
        $this->writer->setDescription(isset($options['description']) ? $options['description'] : $container->getParameter('bloginy.description'));
        
        $this->writer->addAuthor(
            isset($options['author_name']) ? $options['author_name'] : $container->getParameter('bloginy.title'), 
            isset($options['author_email']) ? $options['author_email'] : $container->getParameter('bloginy.email'), 
            isset($options['author_web_site']) ? $options['author_web_site'] : $container->getParameter('bloginy.link'));
        
        $this->type = $type;
        $this->container = $container;
    }
    
    public function generate()
    {
        return $this->writer->export($this->type);
    }
}