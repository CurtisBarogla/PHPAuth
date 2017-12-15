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

namespace Zoe\Component\Security\User\Storage;

use Zoe\Component\Security\User\AuthenticatedUserInterface;
use Zoe\Component\Security\Common\LazyLoadingTrait;

/**
 * Wrapper around UserStorage implementations.
 * Should increase performances in certain cases
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class LazyLoadingStorageWrapper implements UserStorageInterface
{
    
    use LazyLoadingTrait;
    
    /**
     * Wrapped storage
     * 
     * @var UserStorageInterface
     */
    private $wrapped;
    
    /**
     * Initialize wrapper
     * 
     * @param UserStorageInterface $wrapped
     *   Storage to wrap
     */
    public function __construct(UserStorageInterface $wrapped)
    {
        $this->wrapped = $wrapped;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::addUser()
     */
    public function addUser(string $identifier, AuthenticatedUserInterface $user): void
    {
        $this->wrapped->addUser($identifier, $user);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::getUser()
     */
    public function getUser(string $identifier): AuthenticatedUserInterface
    {
        return $this->_lazyLoad("WrappedUserStorageStoredUser", true, [$this->wrapped, "getUser"], $identifier);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::deleteUser()
     */
    public function deleteUser(string $identifier): void
    {
        $this->wrapped->deleteUser($identifier);
    }
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::refreshUser()
     */
    public function refreshUser(string $identifier, AuthenticatedUserInterface $user): void
    {
        $this->wrapped->refreshUser($identifier, $user);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Storage\UserStorageInterface::isStored()
     */
    public function isStored(string $identifier): bool
    {
        return $this->wrapped->isStored($identifier);
    }

}
