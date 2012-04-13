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

namespace Rizeway\BloginyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Rizeway\BloginyBundle\Model\Utils\SlugGenerator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Yaml\Parser;

class UpdateBloginyFrom2To3Command extends ContainerAwareCommand
{

    protected function configure()
    {
        parent::configure();

        $this->setName('bloginy:update-2-to-3')
             ->setDescription('Updates bloginy project from 2.3 to 3.0');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        \set_time_limit(0);
        
        // Renaming old tables
       /* $output->writeln('<info>-- Renaming actual tables ...</info>');
        $this->renameOldTables();
        $output->writeln('<info>Old Tables renamed</info>');
        
        // Doctrine Creating Schema
        $output->writeln('<info>-- Creating the new schema ...</info>');
        $command = $this->getApplication()->find('doctrine:schema:create');
        $command->run(new ArrayInput(array('command' => 'creating new schema')), $output);
        $output->writeln('<info>New Schema created</info>');
        
        // Fixtures
        $output->writeln('<info>-- Loading fixtures ...</info>');
        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $command->run(new ArrayInput(array('command' => 'loading data test')), $output);  
        $output->writeln('<info>fixtures loaded</info>');
        
        // Tables migration
        $output->writeln('<info>-- Migrating tables ...</info>');
        $output->writeln('User Table ...');
        $this->migrateUserTable();
        $output->writeln('Blog Table ...');
        $this->migrateBlogTable();
        $output->writeln('Blog Post Table ...');
        $this->migrateBlogPostTable();
        $output->writeln('Post Table ...');
        $this->migratePostTable();
        $output->writeln('Vote Table ...');
        $this->migrateVoteTable();
        $output->writeln('Tag Table ...');
        $this->migrateTagTable();
        $output->writeln('Comment Table ...');
        $this->migrateCommentTable();
        $output->writeln('Activity Table ...');
        $this->migrateActivityTable();
        $output->writeln('Api Log Table ...');
        $this->migrateApiLogTable();
        $output->writeln('Page Table ...');
        $this->migratePageTable();
        $output->writeln('<info>tables migrated...</info>');
        
        // Removing old tables 
        $output->writeln('<info>-- Deleting old tables ...</info>');
        $this->deleteOldTables();
        $output->writeln('<info>Old Tables deleted</info>');
        */
        // Updating avatars
        $output->writeln('<info>-- Updating Avatars ...</info>');
        $this->migrateAvatars();
        $output->writeln('<info>Avatars Updated</info>');

        
        return 0;
    }

    private function renameOldTables()
    {
        $tables = array('activity', 'blog', 'feed', 'blog_feed', 'tag', 'vote', 'bloginy_option', 'comment', 'log_api', 'user', 'user_blog', 'visit');
        
        $sqls = array();
        foreach ($tables as $table)
        {
            $sqls[] = $table.' TO '.$table.'_old';
        }
        
        $sql = 'RENAME TABLE '.\implode(', ', $sqls);
        $connection = $this->getContainer()->get('doctrine')->getEntityManager()
             ->getConnection();
        $connection->exec($sql);
    }
    
    private function deleteOldTables()
    {
        $tables = array('activity', 'blog', 'feed', 'blog_feed', 'tag', 'vote', 'bloginy_option', 'comment', 'log_api', 'user', 'user_blog', 'visit');
        
        $sqls = array();
        foreach ($tables as $table)
        {
            $sqls[] = 'DROP TABLE '. $table.'_old;';
        }
        
        $sql = \implode(' ', $sqls);
        $connection = $this->getContainer()->get('doctrine')->getEntityManager()
             ->getConnection();
        $connection->exec($sql);
    }
    
    private function migrateUserTable()
    {
        $connection = $this->getContainer()->get('doctrine')->getEntityManager()
             ->getConnection();
        
        // User Migration
        $sql ='
            INSERT INTO User 
                (id, username, password, email, last_name, first_name, birthday, avatar, use_gravatar, web_site, api_code, approved, created_at, twitter)
            SELECT
                id, username, password, email, nom, prenoms, date_naissance, avatar, 0, site_web, api_code, validated, created_at, twitter
            FROM user_old';
        $connection->exec($sql);
        
        // Waiting User Migration
        $sql ='
            INSERT INTO UserActivation
                (user_id, code, count_tries, locked, created_at)
            SELECT
                id, code_validation, 0, 0, created_at
            FROM user_old 
            WHERE validated = 0';
        $connection->exec($sql);
    }
    
    private function migrateBlogTable()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // Blog Migration
        $sql ='
            INSERT IGNORE INTO Blog 
                (id, title, description, slug, url, feed_url, location, approved, rank_value, votes, count_views, created_at, language)
            SELECT
                id, title, description, id, url, feed, wilaya, validated, popularite, 0, 0, created_at, "fr"
            FROM blog_old';
        $connection->exec($sql);
        
        // Slug & Location
        $parser = new Parser();
        $translations = $parser->parse(file_get_contents(__DIR__.'/../Resources/translations/messages.fr_FR.yml'));
        $translations = array_flip($translations) + array(
            'Tizi-Ouzou' => 'Tizi Ouzou', 
            "Ghardaia" => "Ghardaïa",
            "Oum-El-Bouaghi" => "Oum El-Bouaghi",
            "Ain-Defla" => "Aïn Defla",
            "Sidi-Bel-Abbes" => "Sidi Bel Abbes",
            "Bordj-Bou-Arreridj" => "Bordj-Bou-Arreridj",
            "Ain-Temouchent" => "Aïn Temouchent");
        $slugGenerator = new SlugGenerator($em->getRepository('BloginyBundle:Blog'));
        foreach ($em->getRepository('BloginyBundle:Blog')->findAll() as $blog)
        {
            if (isset($translations[$blog->getLocation()])) {
                $blog->setLocation($translations[$blog->getLocation()]);   
             }
            $blog->setSlug($slugGenerator->generateUniqueSlug($blog->getTitle()));
            $em->flush();
        }
        
        $em->clear();
    }
    
    private function migrateBlogPostTable()
    {
        $momory_limit = \ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // Blog Post Migration
        $sql ='
            INSERT INTO BlogPost
                (id, blog_id, title, slug, content, link, approved, published_at, created_at, language)
            SELECT
                blog_feed_old.id, blog_feed_old.blog_id, blog_feed_old.title, blog_feed_old.id, blog_feed_old.description, blog_feed_old.link, blog_feed_old.hidden, blog_feed_old.publicated_at, blog_feed_old.publicated_at, "other"
            FROM blog_feed_old JOIN Blog ON (blog_feed_old.blog_id = Blog.id) WHERE blog_feed_old.title != "" AND blog_feed_old.title != "Gaza, Photos de la Manifestation à Annaba" AND (blog_feed_old.title != "Presentation de mon blog" OR blog_feed_old.blog_id != 39)';
        $connection->exec($sql);
        
        // Slug
        $slugGenerator = new SlugGenerator($em->getRepository('BloginyBundle:BlogPost'));
        for ($i = 0; $i < 5; $i++)
        {
            $slugs = array();
            foreach ($em->getRepository('BloginyBundle:BlogPost')->findAll() as $post)
            {
                if (is_numeric($post->getSlug())) {
                    $slug = $slugGenerator->generateUniqueSlug($post->getTitle());
                    
                    if (!isset($slugs[$slug])) {
                        $slugs[$slug] = 1;
                        $post->setSlug($slug);
                    }
                }
            }
            
            $em->flush();
            $em->clear();
        }
        ini_set('memory_limit', $momory_limit);
    }
    
    private function migratePostTable()
    {
        $momory_limit = \ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // Post Migration
        $sql ='
            INSERT INTO Post
                (id, user_id, title, slug, content, link, count_votes, rank_value, count_views,approved,  created_at, language)
            SELECT
                feed_old.id, feed_old.user_id, feed_old.title, feed_old.id, feed_old.description, feed_old.link, feed_old.points, feed_old.rank_points, feed_old.popularite, feed_old.hidden,  feed_old.created_at, "other"
            FROM feed_old JOIN User On (feed_old.user_id = User.id)';
        $connection->exec($sql);
        
        // Slug
        $slugGenerator = new SlugGenerator($em->getRepository('BloginyBundle:Post'));
        for ($i = 0; $i < 10; $i++)
        {
            $slugs = array();
            foreach ($em->getRepository('BloginyBundle:Post')->findAll() as $post)
            {
                if (is_numeric($post->getSlug())) {
                    $slug = $slugGenerator->generateUniqueSlug($post->getTitle());
                    
                    if (!isset($slugs[$slug])) {
                        $slugs[$slug] = 1;
                        $post->setSlug($slug);
                    }
                }
            }
            
            $em->flush();
            $em->clear();
        }
        
        // Migrate the relations between BlogPost And Post
        $sql = '
            UPDATE IGNORE BlogPost, blog_feed_old, Post SET BlogPost.post_id = blog_feed_old.feed_id WHERE 
                BlogPost.id = blog_feed_old.id AND 
                blog_feed_old.proposed = "1" AND
                blog_feed_old.feed_id = Post.id';
        $connection->exec($sql);
        
        ini_set('memory_limit', $momory_limit);
    }
    
    private function migrateVoteTable()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // Vote Migration
        $sql ='
            INSERT INTO Vote
                (post_id, user_id, created_at)
            SELECT
                vote_old.feed_id, vote_old.user_id, vote_old.created_at
            FROM vote_old JOIN User ON (vote_old.user_id = User.id) JOIN Post ON (vote_old.feed_id = Post.id)';
        $connection->exec($sql);
    }
    
    private function migrateTagTable()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // Tag Migration
        $sql ='
            INSERT INTO Tag
                (post_id, tag)
            SELECT
                tag_old.feed_id, tag_old.tag
            FROM tag_old JOIN Post ON (tag_old.feed_id = Post.id)';
        $connection->exec($sql);
        
        $connection->exec('UPDATE Tag set tag= REPLACE(tag, "[dot]", ".")');
        $connection->exec('UPDATE Tag set tag= REPLACE(tag, "[slash]", "/")');
    }
    
    private function migrateCommentTable()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // Comment Migration
        $sql ='
            INSERT INTO Comment
                (id, user_id, post_id, username, email, web_site, comment, approved, created_at)
            SELECT
                comment_old.id, User.id, comment_old.feed_id, comment_old.pseudo, comment_old.email, comment_old.site_web, comment_old.commentaire, comment_old.validated, comment_old.created_at
            FROM comment_old LEFT JOIN User ON (comment_old.user_id = User.id) JOIN Post ON (comment_old.feed_id = Post.id)';
        $connection->exec($sql);
        
        $sql ='
            UPDATE Post 
            SET Post.count_comments = 
                (SELECT COUNT(Comment.id) FROM Comment
                 WHERE Comment.post_id = Post.id AND Comment.approved = 1)';
        $connection->exec($sql);
    }
    
    private function migrateActivityTable()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // Activity Migration
        $sql ='
            INSERT INTO Activity
                (post_id, user_id, type, approved, created_at)
            SELECT
                activity_old.foreign, activity_old.user_id, "new_vote", 1, activity_old.created_at
            FROM activity_old JOIN User ON (activity_old.user_id = User.id) JOIN Post ON (activity_old.foreign = Post.id)
            WHERE activity_old.type = "VoteCreation"';
        $connection->exec($sql);
        
        $sql ='
            INSERT INTO Activity
                (post_id, user_id, type, approved, created_at)
            SELECT
                activity_old.foreign, activity_old.user_id, "new_post", 1, activity_old.created_at
            FROM activity_old JOIN User ON (activity_old.user_id = User.id) JOIN Post ON (activity_old.foreign = Post.id)
            WHERE activity_old.type = "FeedCreation"';
        $connection->exec($sql);
        
        $sql ='
            INSERT INTO Activity
                (user_id, type, approved, created_at)
            SELECT
                activity_old.user_id, "new_user", 1, activity_old.created_at
            FROM activity_old JOIN User ON (activity_old.user_id = User.id)
            WHERE activity_old.type = "UserCreation"';
        $connection->exec($sql);
        
        $sql ='
            INSERT INTO Activity
                (blog_id, type, approved, created_at)
            SELECT
                activity_old.foreign, "new_blog", 1, activity_old.created_at
            FROM activity_old JOIN Blog ON (activity_old.foreign = Blog.id)
            WHERE activity_old.type = "BlogCreation"';
        $connection->exec($sql);
        
        $sql ='
            INSERT INTO Activity
                (comment_id, post_id, user_id, type, approved, created_at)
            SELECT
                activity_old.foreign, Post.id, User.id, "new_comment", 1, activity_old.created_at
            FROM activity_old 
                JOIN Comment ON (activity_old.foreign = Comment.id)
                JOIN Post ON (Comment.post_id = Post.id)
                LEFT JOIN User ON (Comment.user_id = User.id)
            WHERE activity_old.type = "CommentCreation"';
        $connection->exec($sql);
    }
    
    private function migrateApiLogTable()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // ApiLog Migration
        $sql ='
            INSERT INTO ApiLog
                (user_id, function, client, created_at)
            SELECT
                log_api_old.user_id, log_api_old.fonction, log_api_old.client, log_api_old.created_at
            FROM log_api_old JOIN User ON (log_api_old.user_id = User.id)';
        $connection->exec($sql);
    }
    
    private function migratePageTable()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $connection = $em->getConnection();
        
        // Page Creation
        $sql ='
            INSERT INTO Page
                (user_id, title, slug, created_at, public)
            SELECT
                User.id, CONCAT("Blogs suivis par ", User.username), CONCAT("page-blogs-", User.username), User.created_at, 0
            FROM User
            JOIN user_blog_old ON (User.id = user_blog_old.user_id)
            GROUP BY User.id';
        $connection->exec($sql);
        
        // Blogs Migration
        $sql ='
            INSERT INTO page_blog
                (page_id, blog_id)
            SELECT
              Page.id, Blog.id
            FROM Blog
            JOIN user_blog_old ON (Blog.id = user_blog_old.blog_id)
            JOIN Page ON (user_blog_old.user_id = Page.user_id)
            GROUP BY Page.id, Blog.id';
        $connection->exec($sql);
    }
    
    private function migrateAvatars()
    {
        $momory_limit = \ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $users = $em->getRepository('UserBundle:User')->findAll();
        foreach ($users as $user)
        {
            $avatar = $user->getAvatar();
            //if (!$avatar->getUseGravatar())
            //{
                if (strpos($avatar->getPath(), '/no-avatar.') <= 0)
                {
                    if (\is_file(__DIR__.'/../../../../web'.$avatar->getPath()))
                    {
                        $avatar->setPath(__DIR__.'/../../../../web'.$avatar->getPath());
                        $avatar->file = new File($avatar->getPath());
                        $avatar->update($user->getUsername().'_avatar');
                        $user->setAvatar($avatar);
                    }
                    else
                    {
                        $avatar->setUseGravatar(true);
                        $avatar->setPath(null);
                        $user->setAvatar($avatar);
                    }
                }
                else
                {
                    $avatar->setUseGravatar(true);
                    $avatar->setPath(null);
                    $user->setAvatar($avatar);
                }
            //}
        }
        $em->flush();
        ini_set('memory_limit', $momory_limit);
    }
    
}