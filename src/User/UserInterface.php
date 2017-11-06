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

use Zoe\Component\Security\Exception\InvalidUserAttributeException;

/**
 * Basic user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface UserInterface
{
    
    /**
     * Get the user identifier
     * 
     * @return string
     *   User identifier
     */
    public function getName(): string;
    
    /**
     * Get the user password
     * 
     * @return string
     *   User password
     */
    public function getPassword(): string;
    
    /**
     * Get all roles for the user
     * 
     * @return array
     *   All roles associated to the user
     */
    public function getRoles(): array;
    
    /**
     * Check if the user has a specified role
     * 
     * @param string $role
     *   Role name
     * 
     * @return bool
     *   True if the user has the specified role. False otherwise
     */
    public function hasRole(string $role): bool;
    
    /**
     * Check if the user is considered root.
     * In other words, the root user has all access into the application
     * 
     * @return bool
     *   True if the user is root. False otherwise
     */
    public function isRoot(): bool;
    
    /**
     * Get all extra attributes setted.
     * Return null if no attributes has been setted
     * 
     * @return array|null
     *   All extra attributes
     */
    public function getAttributes(): ?array;
        
    /**
     * Get a specific user attribute
     * 
     * @param string $name
     *   Attribute name
     *   
     * @return mixed
     *   Attribute value
     *   
     * @throws InvalidUserAttributeException
     *   If the requested attribute is not setted
     */
    public function getAttribute(string $name);
    
    /**
     * Check if an attribute is setted into the user
     * 
     * @param string $name
     *   Attribute name
     * 
     * @return bool
     *   True if the user has the given attribute. False otherwise
     */
    public function hasAttribute(string $name): bool;
    
}
