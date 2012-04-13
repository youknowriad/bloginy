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

namespace Rizeway\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Rizeway\UserBundle\Lib\Encoder\Md5Encoder;
use Rizeway\UserBundle\Lib\Utils\Avatar;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Rizeway\UserBundle\Entity\User
 */
class User implements AdvancedUserInterface
{

    const ROLE_REGISTRED_USER = 'ROLE_REGISTRED_USER';
    const ROLE_ADMIN_USER = 'ROLE_ADMIN_USER';

    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var string $email
     */
    private $email;

    /**
     * @var string $last_name
     */
    private $last_name;

    /**
     * @var string $first_name
     */
    private $first_name;

    /**
     * @var date $birthday
     */
    private $birthday;

    /**
     * @var string $avatar_path
     */
    private $avatar_path;

    /**
     * @var boolean $use_gavatar
     */
    private $use_gravatar = true;

    /**
     * @var string $web_site
     */
    private $web_site;

    /**
     * @var string $twitter
     */
    private $twitter;

    /**
     * @var string $facebook
     */
    private $facebook;

    /**
     * @var string $api_code
     */
    private $api_code;

    /**
     * @var boolean $approved
     */
    private $approved = false;

    /**
     * @var datetime $created_at
     */
    private $created_at;
    
    /**
     * @var string $locale
     */
    private $locale;
    
    /**
     * @var string $extra_roles
     */
    private $extra_roles;

    /**
     * @var integer $id
     */
    private $id;

    public function  __construct()
    {
        $this->created_at = new \DateTime();
        $this->votes = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {        
        $encoder = new Md5Encoder();
        $this->password = $encoder->encodePassword($password, $this->getSalt());
    }

    /**
     * Get password
     *
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set last_name
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->last_name = $lastName;
    }

    /**
     * Get last_name
     *
     * @return string $lastName
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * Set first_name
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;
    }

    /**
     * Get first_name
     *
     * @return string $firstName
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set birthday
     *
     * @param date $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Get birthday
     *
     * @return date $birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set avatar
     *
     * @param string $avatar_path
     */
    public function setAvatarPath($avatar_path)
    {
        $this->avatar_path = $avatar_path;
    }

    /**
     * Get avatar_path
     *
     * @return string $avatar_path
     */
    public function getAvatarPath()
    {
        return $this->avatar_path;
    }

    /**
     * Set use_gravatar
     *
     * @param boolean $useGravatar
     */
    public function setUseGravatar($useGravatar)
    {
        $this->use_gravatar = $useGravatar;
    }

    /**
     * Get use_gravatar
     *
     * @return boolean $useGravatar
     */
    public function getUseGravatar()
    {
        return $this->use_gravatar;
    }

    /**
     * Set web_site
     *
     * @param string $webSite
     */
    public function setWebSite($webSite)
    {
        $this->web_site = $webSite;
    }

    /**
     * Get web_site
     *
     * @return string $webSite
     */
    public function getWebSite()
    {
        return $this->web_site;
    }

    /**
     * Set twitter
     *
     * @param string $twitter
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * Get twitter
     *
     * @return string $twitter
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * Set facebook
     *
     * @param string $facebook
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * Get facebook
     *
     * @return string $facebook
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * Set api_code
     *
     * @param string $apiCode
     */
    public function setApiCode($apiCode)
    {
        $this->api_code = $apiCode;
    }

    /**
     * Get api_code
     *
     * @return string $apiCode
     */
    public function getApiCode()
    {
        return $this->api_code;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * Get approved
     *
     * @return boolean $approved
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * Set locale
     *
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Get locale
     *
     * @return string $locale
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    /**
     * Set Extra roles
     *
     * @param string $roles
     */
    public function setExtraRoles($extra_roles)
    {
        $this->extra_roles = $extra_roles;
    }

    /**
     * Get Extra Roles
     *
     * @return string $roles
     */
    public function getExtraRoles()
    {
        return $this->extra_roles;
    }

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getRoles()
    {
        $extra_roles = \explode(',', $this->getExtraRoles());
        
        return array_merge(array(self::ROLE_REGISTRED_USER), $extra_roles);
    }

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {

    }
        
    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    
    public function isEnabled()
    {
        return $this->approved;
    }

    public function equals(UserInterface $account)
    {
        return ($account->getUsername() === $this->getUsername());
    }

    /**
     * @return Avatar
     */
    public function getAvatar()
    {
        $avatar = new Avatar();

        $avatar->setPath($this->getAvatarPath());
        $avatar->setUseGravatar($this->getUseGravatar());

        return $avatar;
    }

    /**
     * Set the new avatar
     * @param Avatar $avatar
     */
    public function setAvatar(Avatar $avatar)
    {
        $this->setAvatarPath($avatar->getPath());
        $this->setUseGravatar($avatar->getUseGravatar());
    }
    
    public function getName()
    {
        if ($this->first_name || $this->last_name)
        {
            return $this->first_name.' '.$this->last_name;
        }
        else
        {
            return $this->username;
        }
    }
}