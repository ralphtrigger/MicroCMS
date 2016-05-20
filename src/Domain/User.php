<?php

namespace MicroCMS\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Description of User
 *
 * @author trigger
 */
class User implements UserInterface
{
    /**
     * User id.
     * 
     * @var int
     */
    private $id;

    /**
     * User name.
     * 
     * @var string
     */
    private $username;

    /**
     * User password.
     * 
     * @var string
     */
    private $password;

    /**
     * Salt that was originaly use to encode the password.
     * 
     * @var string
     */
    private $salt;

    /**
     * Role.
     * Values : ROLE_USER or ROLE_ADMIN.
     * 
     * @var string
     */
    private $role;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->getRole());
    }

}
