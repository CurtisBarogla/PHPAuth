<?php
//StrictType
declare(strict_types = 1);

/*
 * Zoe
 * Security component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace Zoe\Component\Security\Storage;

use Zoe\Component\Security\Exception\LogicException;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;

/**
 * Use native Session ($_SESSION) array as storage
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeSessionStorage implements UserStorageInteface
{
    
    /**
     * $_SESSION
     * 
     * @var array
     */
    private $session;
    
    /**
     * Refresh session id
     * 
     * @var true
     */
    private $refresh;
    
    /**
     * Initialize the store
     * Session MUST be active
     * 
     * @param bool $refresh
     *   Set to true to refresh the session id when a user is added or refreshed
     */
    public function __construct(bool $refresh = true)
    {
        $this->session = &$_SESSION;
        $this->refresh = $refresh;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::addUser()
     */
    public function addUser(string $userIdentifier, StorableUserInterface $user): void
    {
        if($this->refresh)
            \session_regenerate_id();
        
        $this->session[$userIdentifier] = $user;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::getUser()
     */
    public function getUser(string $userIdentifier): StorableUserInterface
    {
        $this->checkUserIdentifier($userIdentifier);
          
        return $this->session[$userIdentifier];
    }

    /**
     * @throws LogicException
     *   Cannot call delete user on native storage as php clear the session on expire
     */
    public function deleteUser(string $userIdentifier): void
    {
        throw new LogicException("Cannot delete user from this storage as php is responsible to expire invalid sessions");
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::refreshUser()
     */
    public function refreshUser(string $userIdentifier, StorableUserInterface $user): void
    {
        $this->checkUserIdentifier($userIdentifier);
        
        $this->addUser($userIdentifier, $user);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::hasUser()
     */
    public function hasUser(string $userIdentifier): bool
    {
        return isset($this->session[$userIdentifier]);
    }
    
    /**
     * Check if a user is stored
     * 
     * @param string $identifier
     *   User identifier key
     * 
     * @throws UserNotFoundException
     *   When no user is stored for this identifier
     */
    private function checkUserIdentifier(string $identifier): void
    {
        if(!isset($this->session[$identifier]))
            throw new UserNotFoundException(\sprintf("No user found into the store for this identifier '%s'",
                $identifier));
    }

}
