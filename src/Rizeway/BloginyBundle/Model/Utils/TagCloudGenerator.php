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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Rizeway\BloginyBundle\Entity\Post;
use Rizeway\BloginyBundle\Entity\BlogPost;
use Rizeway\BloginyBundle\Entity\Blog;
use Rizeway\UserBundle\Entity\User;

class TagCloudGenerator
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }


    /**
     * Get Tag Cloud for top posts
     *
     * @param Category $category
     * @param string $sort
     * @param string $language
     * 
     * @return int[]
     */
    public function getTagCloudForTopPosts($category, $sort, $language)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $qb = $this->getBaseQueryBuilder();
        $qb = $em->getRepository('BloginyBundle:Post')->getTopQueryBuilder($category, $sort, $language, $qb);
        
        return $this->getTagCloudForQueryBuilder($qb);
    }
    
    public function getTagCloudForSearchFilter($filter)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $qb = $this->getBaseQueryBuilder();
        $qb = $em->getRepository('BloginyBundle:Post')->getSearchFilterQueryBuilder($filter, $qb);

        return $this->getTagCloudForQueryBuilder($qb);
        
    }

    /**
     * Get The tag cloud for the live page
     * 
     * @return int[]
     */
    public function getTagCloudForLive()
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $qb = $this->getBaseQueryBuilder();

        return $this->getTagCloudForQueryBuilder($qb);
    }

    /**
     * Get Tag Cloud for a blog
     * 
     * @param Blog $blog
     * @return int[]
     */
    public function getTagCloudForBlog(Blog $blog)
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $qb = $this->getBaseQueryBuilder(false);
        $qb->join('post.blog_post', 'blog_post');
        $qb = $em->getRepository('BloginyBundle:BlogPost')->getBlogQueryBuilder($blog, $qb);
        
        return $this->getTagCloudForQueryBuilder($qb);
    }
    
    /**
     * Get Tag Cloud for a user
     *
     * @param Blog $blog
     * @return int[]
     */
    public function getTagCloudForUser(User $user)
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $qb = $this->getBaseQueryBuilder(false);
        $qb = $em->getRepository('BloginyBundle:Post')->getUserQueryBuilder($user, $qb);

        return $this->getTagCloudForQueryBuilder($qb);
    }

    /**
     * Get the tag cloud for posts having tag
     * 
     * @param string $tag
     * @return int[]
     */
    public function getTagCloudForTag($tag)
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $qb = $this->getBaseQueryBuilder(false);
        $qb->join('post.tags', 'tags', Expr\Join::WITH, 'tags.tag = :tag')
           ->setParameter('tag', $tag);

        return $this->getTagCloudForQueryBuilder($qb);
    }

    /**
     * Get Tag Cloud for Query Builder
     *
     * @param QueryBuilder $qb
     * @return int[]
     */
    public function getTagCloudForQueryBuilder(QueryBuilder $qb)
    {
        $qb->resetDQLPart('orderBy');
        $qb->orderBy('quantity', 'DESC');
        $query = $qb->getQuery();

        $query->setMaxResults($this->container->getParameter('bloginy.tag_cloud.limit'));
        $result = $query->getResult();

        return $this->formatTags($result);
    }

    /**
     * Get The base tag Query Builder
     *
     * @param bool $time_limit
     * @return QueryBuilder 
     */
    private function getBaseQueryBuilder($time_limit = true)
    {
        $em = $this->container->get('doctrine')->getEntityManager();
        $qb = new QueryBuilder($em);
        $qb->select('tag.tag, count(tag.post) as quantity')
           ->from('BloginyBundle:Tag', 'tag')
           ->join('tag.post', 'post')
           ->addGroupBy('tag.tag');

        if ($time_limit)
        {
            $date = new \DateTime();
            $date->modify('- 6 months');

            $qb = $em->getRepository('BloginyBundle:Post')->getCreatedAtQueryBuilder(
                $date, Operators::OPERATOR_GREATER_THAN, $qb);
        }
        
        return $qb;
    }

    /**
     * Format the tags like this tag => font_size
     * 
     * @param string[][] $tags
     * @return int[]
     */
    private function formatTags($tags)
    {
        if (!count($tags))
        {
          return $tags;
        }

        $tags_formatted = array();
        foreach ($tags as $tag)
        {
            $tags_formatted[$tag['tag']] = $tag['quantity'];
        }
        
        // Taille maximal - Taille minimal
        $max_size = $this->container->getParameter('bloginy.tag_cloud.tag_max_size'); // max font size en %
        $min_size = $this->container->getParameter('bloginy.tag_cloud.tag_min_size'); // min font size en %

        // Obtenir la plus petit valeur et la plus grande du tableau
        $max_qty = max(array_values($tags_formatted));
        $min_qty = min(array_values($tags_formatted));

        $keys = \array_keys($tags_formatted); 
        \shuffle($keys); 
        $tags_formatted = \array_merge(\array_flip($keys), $tags_formatted); 

        // Déterminer l'étendu des valeurs
        $spread = $max_qty - $min_qty;
        if (0 == $spread)
        {
            $spread = 1;
        }


        // Détermine l'incrémentation du font-size
        $step = ($max_size - $min_size) / ($spread);


        // Boucle à travers le tableau
        $tags = array();
        foreach ($tags_formatted as $key=>$value)
        {
            $size = $min_size + (($value - $min_qty) * $step);
            $tags[$key] = $size;
        }

        return $tags;
    }
}