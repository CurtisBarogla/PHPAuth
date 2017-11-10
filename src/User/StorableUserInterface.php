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

namespace Zoe\Component\Security\User;

/**
 * Describe a user that can be setted into a store
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface StorableUserInterface
{
    
    /**
     * Identifier to identified the user into a store
     *
     * @var string
     */
    public const USER_STORE_IDENTIFIER = "USER_ID";
    
    /**
     * Get the user identifier
     * 
     * @return string
     *   User identifier
     */
    public function getName(): string;
    
    /**
     * Check if the is root
     * 
     * @return bool
     *   True if the user if root. False otherwise
     */
    public function isRoot(): bool;
    
    /**
     * Get all roles for the user
     * 
     * @return array
     *   All user roles 
     */
    public function getRoles(): array;
    
    /**
     * Get all attribute for the user
     * 
     * @return array|null
     *   All attributes setted into the users
     */
    public function getAttributes(): ?array;
    
}
