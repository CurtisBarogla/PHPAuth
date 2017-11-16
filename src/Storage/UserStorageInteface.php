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

use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;

/**
 * Responsible to storing user data during the session duration
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface UserStorageInteface
{
    
    /**
     * Identifier to interacting with a user storage
     *
     * @var string
     */
    public const STORE_USER_ID = "USER_ID";
    
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
    
    /**
     * Check if a user is present into the storage for the given identifier
     * 
     * @param string $userIdentifier
     *   True if a user is stored for the given identifier. False otherwise
     * @return bool
     */
    public function hasUser(string $userIdentifier): bool;
    
}
