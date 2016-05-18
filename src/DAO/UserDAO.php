<?php

namespace MicroCMS\DAO;

use MicroCMS\Domain\User;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Description of UserDAO
 *
 * @author trigger
 */
class UserDAO extends DAO implements UserProviderInterface
{
    public function find($id)
    {
        $sql = "select * from t_user where usr_id=?";
        $row = $this->getDb()->fetchAssoc($sql, [$id]);

        if ($row) {
            return $this->buildDomainObject($row);
        }
        else {
            throw new Exception("No user matching id ".$id);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $sql = "select * from t_user where usr_name=?";
        $row = $this->getDb()->fetchAssoc($sql, [$username]);

        if ($row) {
            return $this->buildDomainObject($row);
        }
        else {
            throw new UsernameNotFoundException(
            sprintf('User "%s" not found.', $username));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
            sprintf('Instance of "%s" are not supported.', $class));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return 'MicroCMS\Domain\User' === $class;
    }
    /**
     * Create User object based on DB row.
     * 
     * @param array $row The DB row containing User data.
     * @return \MicroCMS\Domain\User
     */
    protected function buildDomainObject($row)
    {
        $user = new User();
        
        $user->setId($row['usr_id']);
        $user->setUsername($row['usr_name']);
        $user->setPassword($row['usr_password']);
        $user->setRole($row['usr_role']);
        $user->setSalt($row['usr_salt']);
        
        return $user;
    }

}
