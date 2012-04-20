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

namespace Rizeway\BloginyBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Rizeway\BloginyBundle\Entity\Post;
use Rizeway\BloginyBundle\Entity\Vote;
use Rizeway\BloginyBundle\Entity\Tag;
use Rizeway\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class PostData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $user = $this->getReference('admin-user');
        $youknowriad = $this->getReference('simple-user');
        $category_technology = $manager->getRepository('BloginyBundle:Category')->findOneBy(array('name' => 'Technology'));
        $category_sports = $manager->getRepository('BloginyBundle:Category')->findOneBy(array('name' => 'Sports'));

        // First post
        $post = new Post();
        $post->setTitle('Twitious, delicious à la sauce Twitter');
        $post->setContent('Vous avez partagé un lien intéréssant sur Twitter il y a un certain temps et vous n’arrivez pas à le retrouver, vous n’êtes pas la seule, et le talentueux développeur Joel Moss nous a concocté un petit service pour ne plus perdre aucun lien supprimé sur Twitter … Je cite Twitious');
        $post->setSlug('twitious-delicious-a-la-sauce-twitter');
        $post->setLink('http://youknowriad.nomade-dz.com/twitious-delicious-a-la-sauce-twitter/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');
        $date = new \DateTime();
        $date->modify('- 30 days');
        $post->setCreatedAt($date);

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Twitter');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Technology');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Twitious');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        $manager->flush();

        $this->addReference('blog-post-from-youknowriad', $post);

        // Second post
        $post = new Post();
        $post->setTitle('L’enjeu du personal branding pour les étudiants');
        $post->setContent('Souvenez vous, il y a quelques semaines j’invitais les étudiants à répondre à un sondage sur le personal branding, dans le cadre d’un projet personnel. La présentation ci-dessous résume un peu les résultats de cette étude, la conclusion est toute simple, « Le personal branding n’est pas une recommandation, c’est une obligation »');
        $post->setSlug('enjeu-personal-branding-pour-etudiants');
        $post->setLink('http://youknowriad.nomade-dz.com/lenjeu-du-personal-branding-pour-les-etudiants/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');
        
        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Students');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Personal Branding');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('INSA');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Third post
        $post = new Post();
        $post->setTitle('M3ak Ya Mourad Meghni معاك يا مراد');
        $post->setContent('Je sors un peu de ma ligne éditoriale pour vous parler de Football. Comme vous le savez l’équipe nationale d’Algérie s’apprête à disputer sa troisième coupe du monde après 24 ans d’absence. Cependant, un gros coup dur vient frapper à la porte des verts, la défection de notre joueur fétiche, le maetro Mourad Meghni.');
        $post->setSlug('m3ak-ya-mourad-meghni-معاك-يا-مراد');
        $post->setLink('http://youknowriad.nomade-dz.com/m3ak-ya-mourad-meghni-معاك-يا-مراد/');
        $post->setUser($user);
        $post->setCategory($category_sports);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Sports');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Football');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Algeria');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Fourth post
        $post = new Post();
        $post->setTitle('iDuelz, des duels entre gamers pour iPhone');
        $post->setContent('iDuelz, Une application très prometteuse qui propose de mettre à l’épreuve les possesseurs d’iPhone et autres iPod et iPad. Le principe est simple, misez de l’argent (virtuel pour l’instant  ), et affrontez vos amis sur les différents jeux de la plateforme (4 minis jeux pour l’instant) et le gagnant empochera le pactole.');
        $post->setSlug('iduelz-des-duels-entre-gamers-iphone');
        $post->setLink('http://youknowriad.nomade-dz.com/iduelz-des-duels-entre-gamers-pour-iphone/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Apple');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('iPhone');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Games');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Fifth post
        $post = new Post();
        $post->setTitle('rbYamli, Plugin Yamli pour TinyMCE');
        $post->setContent('ceci est un plugin pour Tiny MCE');
        $post->setSlug('plugin-tiny-mce');
        $post->setLink('http://youknowriad.nomade-dz.com/rbyamli-plugin-yamli-pour-tinymce/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Tiny MCE');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('HTML');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Web');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Sixth post
        $post = new Post();
        $post->setTitle('Rémunérer les meilleurs contenus du Net');
        $post->setContent('Flattr est le nouveau site pour rémunérer les contenus qui nous plaisent.');
        $post->setSlug('remenurer-meilleurs-contenus-net');
        $post->setLink('http://youknowriad.nomade-dz.com/remunerer-les-meilleurs-contenus-du-net/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Flattr');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Web');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Argent');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Seventh post
        $post = new Post();
        $post->setTitle('Tout le monde sur Foursquare');
        $post->setContent('Foursquare est le nouveau réseau social à la mode');
        $post->setSlug('tout-le-monde-sur-foursquare');
        $post->setLink('http://youknowriad.nomade-dz.com/tout-le-monde-sur-foursquare/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Social Network');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Web');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Foursquare');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);


        // Eight post
        $post = new Post();
        $post->setTitle('Twitious, delicious à la sauce Twitter');
        $post->setContent('Vous avez partagé un lien intéréssant sur Twitter il y a un certain temps et vous n’arrivez pas à le retrouver, vous n’êtes pas la seule, et le talentueux développeur Joel Moss nous a concocté un petit service pour ne plus perdre aucun lien supprimé sur Twitter … Je cite Twitious');
        $post->setSlug('twitious-delicious-a-la-sauce-twitter-2');
        $post->setLink('http://youknowriad.nomade-dz.com/twitious-delicious-a-la-sauce-twitter/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Twitter');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Technology');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Twitious');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Ninth post
        $post = new Post();
        $post->setTitle('L’enjeu du personal branding pour les étudiants');
        $post->setContent('Souvenez vous, il y a quelques semaines j’invitais les étudiants à répondre à un sondage sur le personal branding, dans le cadre d’un projet personnel. La présentation ci-dessous résume un peu les résultats de cette étude, la conclusion est toute simple, « Le personal branding n’est pas une recommandation, c’est une obligation »');
        $post->setSlug('enjeu-personal-branding-pour-etudiants-2');
        $post->setLink('http://youknowriad.nomade-dz.com/lenjeu-du-personal-branding-pour-les-etudiants/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Students');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Personal Branding');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('INSA');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Ten post
        $post = new Post();
        $post->setTitle('M3ak Ya Mourad Meghni معاك يا مراد');
        $post->setContent('Je sors un peu de ma ligne éditoriale pour vous parler de Football. Comme vous le savez l’équipe nationale d’Algérie s’apprête à disputer sa troisième coupe du monde après 24 ans d’absence. Cependant, un gros coup dur vient frapper à la porte des verts, la défection de notre joueur fétiche, le maetro Mourad Meghni.');
        $post->setSlug('m3ak-ya-mourad-meghni-معاك-يا-مراد-2');
        $post->setLink('http://youknowriad.nomade-dz.com/m3ak-ya-mourad-meghni-معاك-يا-مراد/');
        $post->setUser($user);
        $post->setCategory($category_sports);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Sports');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Football');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Algeria');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Eleven post
        $post = new Post();
        $post->setTitle('iDuelz, des duels entre gamers pour iPhone');
        $post->setContent('iDuelz, Une application très prometteuse qui propose de mettre à l’épreuve les possesseurs d’iPhone et autres iPod et iPad. Le principe est simple, misez de l’argent (virtuel pour l’instant  ), et affrontez vos amis sur les différents jeux de la plateforme (4 minis jeux pour l’instant) et le gagnant empochera le pactole.');
        $post->setSlug('iduelz-des-duels-entre-gamers-iphone-2');
        $post->setLink('http://youknowriad.nomade-dz.com/iduelz-des-duels-entre-gamers-pour-iphone/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Apple');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('iPhone');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Games');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Tweleve post
        $post = new Post();
        $post->setTitle('rbYamli, Plugin Yamli pour TinyMCE');
        $post->setContent('ceci est un plugin pour Tiny MCE');
        $post->setSlug('plugin-tiny-mce-2');
        $post->setLink('http://youknowriad.nomade-dz.com/rbyamli-plugin-yamli-pour-tinymce/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Tiny MCE');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('HTML');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Web');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Thirteen post
        $post = new Post();
        $post->setTitle('Rémunérer les meilleurs contenus du Net');
        $post->setContent('Flattr est le nouveau site pour rémunérer les contenus qui nous plaisent.');
        $post->setSlug('remenurer-meilleurs-contenus-net-2');
        $post->setLink('http://youknowriad.nomade-dz.com/remunerer-les-meilleurs-contenus-du-net/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Flattr');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Web');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Argent');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Fourteen post
        $post = new Post();
        $post->setTitle('Tout le monde sur Foursquare');
        $post->setContent('Foursquare est le nouveau réseau social à la mode');
        $post->setSlug('tout-le-monde-sur-foursquare-2');
        $post->setLink('http://youknowriad.nomade-dz.com/tout-le-monde-sur-foursquare/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Social Network');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Web');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Foursquare');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Fifteen post
        $post = new Post();
        $post->setTitle('rbYamli, Plugin Yamli pour TinyMCE');
        $post->setContent('ceci est un plugin pour Tiny MCE');
        $post->setSlug('plugin-tiny-mce-3');
        $post->setLink('http://youknowriad.nomade-dz.com/rbyamli-plugin-yamli-pour-tinymce/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Tiny MCE');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('HTML');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Web');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Sixteen post
        $post = new Post();
        $post->setTitle('Rémunérer les meilleurs contenus du Net');
        $post->setContent('Flattr est le nouveau site pour rémunérer les contenus qui nous plaisent.');
        $post->setSlug('remenurer-meilleurs-contenus-net-3');
        $post->setLink('http://youknowriad.nomade-dz.com/remunerer-les-meilleurs-contenus-du-net/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Flattr');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Web');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Argent');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Seventeen post
        $post = new Post();
        $post->setTitle('Tout le monde sur Foursquare');
        $post->setContent('Foursquare est le nouveau réseau social à la mode');
        $post->setSlug('tout-le-monde-sur-foursquare-3');
        $post->setLink('http://youknowriad.nomade-dz.com/tout-le-monde-sur-foursquare/');
        $post->setUser($user);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');
        
        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($user);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Social Network');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Web');

        $tag3 = new Tag();
        $tag3->setPost($post);
        $tag3->setTag('Foursquare');

        $post->addTags($tag);
        $post->addTags($tag2);
        $post->addTags($tag3);

        $manager->persist($post);

        // Eighteenth Post
        $post = new Post();
        $post->setLink('');
        $post->setTitle('Bloginy 2.3, dernière ligne droite !');
        $post->setContent('<p>Cette version mineure sera vraisemblablement la dernière avant la refonte de <b>bloginy</b> et son passage à l’excitante version 3 qui s’annonce déjà grandiose, avec notamment le support de Symfony 2, je ne vous en dit pas plus mais je laisserai Riad vous en parler au moment venu.
Qui dit version mineure dit correction de bugs … et c’est le cas : cette version 2.3 en corrige pas mal et supporte la dernière version stable en date du framework symfony.
Mais ce n’est pas tout ! Vous apprécierez certainement les nouveaux RDV quotidiens sur le fil twitter de bloginy : Les blogs fraîchement ajoutés sont promus et twittés après leur validation, et à 20h tapante chaque soir (heure locale en Algérie et au Maroc), un twitt promotionnel pour le blog du jour.
Et vous utilisateurs d’internet explorer, on ne vous a pas oublié, on a corrigé les bugs d’affichages apparents sur le menu de navigation et certaines pages du site !
Enfin, les sections « Blogs » et les pages de Blog ont subi un petit lifting, avec une meilleure prise en charge de la popularité. Les formulaires sont plus clairs et vous pouvez maintenant mettre une description plus longue pour les blogs …
Vous pouvez dores et déjà vérifier cela sur Bloginy Algérie et Bloginy Maroc.
Stay tuned !</p>');
        $post->setSlug('bloginy-2-3-ligne-droite');
        $post->setUser($youknowriad);
        $post->setCategory($category_technology);
        $post->setLanguage('fr');

        $vote = new Vote();
        $vote->setPost($post);
        $vote->setUser($youknowriad);

        $post->addVotes($vote);

        $tag = new Tag();
        $tag->setPost($post);
        $tag->setTag('Bloginy');

        $tag2 = new Tag();
        $tag2->setPost($post);
        $tag2->setTag('Web');

        $post->addTags($tag);
        $post->addTags($tag2);

        $manager->persist($post);

        $manager->flush();

        $this->addReference('commented-post', $post);
    }

    public function getOrder()
    {
        return 4;
    }
}