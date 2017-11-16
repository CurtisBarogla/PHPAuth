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

namespace Zoe\Component\Security\User\Contracts;

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
     * Get the user name
     * 
     * @return string
     *   User name
     */
    public function getName(): string;
    
    /**
     * Check if the user is root
     * 
     * @return bool
     *   True if the user if root. False otherwise
     */
    public function isRoot(): bool;
    
    /**
     * Get all roles associate to the user
     * 
     * @return array
     *   All user roles
     */
    public function getRoles(): array;
    
    /**
     * Get all attributes setted for the user
     * 
     * @return array|null
     *   All attributes or null if no attribute has been setted
     */
    public function getAttributes(): ?array;
    
    /**
     * Get a specific attribute from the user
     * 
     * @param string $attribute
     *   Attribute name
     * 
     * @return mixed
     *   Attribute value
     *   
     * @throws InvalidUserAttributeException
     *   If the attribute is not setted
     */
    public function getAttribute(string $attribute);
    
    /**
     * Check if the user has a specific role
     * 
     * @param string $role
     *   Role name
     * 
     * @return bool
     *   True if the user has the role. False otherwise
     */
    public function hasRole(string $role): bool;
    
    /**
     * Check if the user has a specific attribute
     * 
     * @param string $attribute
     *   Attribute name
     * 
     * @return bool
     *   True if the user has the attribute. False otherwise
     */
    public function hasAttribute(string $attribute): bool;
    
    /**
     * Output user name
     * 
     * @return string
     *   User name
     */
    public function __toString(): string;
    
}
