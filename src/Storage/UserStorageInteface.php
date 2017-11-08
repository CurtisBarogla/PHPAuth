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

use Zoe\Component\Security\User\StorableUserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;

/**
 * Storage for user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface UserStorageInteface
{
    
    /**
     * Add a user into the store
     * 
     * @param string $userIdentifier
     *   User identifier key
     * @param StorableUserInterface $user
     *   Storable user instance
     */
    public function addUser(string $userIdentifier, StorableUserInterface $user): void;
    
    /**
     * Get a user by its identifier from the store
     * 
     * @param string $userIdentifier
     *   User identifier key
     * 
     * @return StorableUserInterface
     *   Storable user instance
     *   
     * @throws UserNotFoundException
     *   When the user for this identifier in not stored
     */
    public function getUser(string $userIdentifier): StorableUserInterface;
    
    /**
     * Delete a user from the store
     * 
     * @param string $userIdentifier
     *   User identifier key
     *   
     * @throws UserNotFoundException
     *   When the user for this identifier in not stored
     */
    public function deleteUser(string $userIdentifier): void;
    
    /**
     * Refresh a stored user by a new one
     * 
     * @param string $userIdentifier
     *   User identifier key
     * @param StorableUserInterface $user
     *   New StorableUserInterface instance
     *   
     * @throws UserNotFoundException
     *   When the user for this identifier is not stored
     */
    public function refreshUser(string $userIdentifier, StorableUserInterface $user): void;
    
}
