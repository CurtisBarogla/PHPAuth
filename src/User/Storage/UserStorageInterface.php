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
use Zoe\Component\Security\Exception\User\UserNotFoundException;

/**
 * Responsible to store AuthenticatedUser for the duration of user session
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface UserStorageInterface
{
    
    /**
     * Add a user into the store
     * 
     * @param string $identifier
     *   User store identifier
     * @param AuthenticatedUserInterface $user
     *   Authenticated user to store
     */
    public function addUser(string $identifier, AuthenticatedUserInterface $user): void;
    
    /**
     * Get a user from the store
     * 
     * @param string $identifier
     *   User store identifier
     *   
     * @return AuthenticatedUserInterface
     *   Authenticated user from the store
     *   
     * @throws UserNotFoundException
     *   When no user has been stored for the given identifier
     */
    public function getUser(string $identifier): AuthenticatedUserInterface;
    
    /**
     * Delete a user from the store
     * 
     * @param string $identifier
     *   User store identifier
     *   
     * @throws UserNotFoundException
     *   When no user has been stored for the given identifier
     */
    public function deleteUser(string $identifier): void;
    
    /**
     * Refresh an already stored user
     * 
     * @param string $identifier
     *   User store identifier
     * @param AuthenticatedUserInterface $user
     *   Refreshed user
     *   
     * @throws UserNotFoundException
     *   When no user has been stored for the given identifier
     */
    public function refreshUser(string $identifier, AuthenticatedUserInterface $user): void;
    
    /**
     * Check into the store if a user corresponds to the given identifier
     * 
     * @param string $identifier
     *   User store identifier
     * 
     * @return bool
     *   True if a user is store. False otherwise
     */
    public function isStored(string $identifier): bool;
    
}
