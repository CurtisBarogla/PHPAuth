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

use Zoe\Component\Security\Exception\User\InvalidUserAttributeException;

/**
 * Basic user.
 * User has a name, attributes and roles.
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface UserInterface
{
    
    /**
     * Get the username
     * 
     * @return string
     *   Username
     */
    public function getName(): string;
    
    /**
     * Add an attribute to the user
     * 
     * @param string $attribute
     *   Attribute name
     * @param mixed $value
     *   Attribute value
     */
    public function addAttribute(string $attribute, $value): void;
    
    /**
     * Get all attributes setted for the user
     * 
     * @return array
     *   All user's attributes
     */
    public function getAttributes(): array;
    
    /**
     * Get an attribute from the user
     * 
     * @param string $attribute
     *   Attribute name
     *   
     * @return mixed
     *   Attribute value
     *   
     * @throws InvalidUserAttributeException
     *   When the given attribute is not setted
     */
    public function getAttribute(string $attribute);
    
    /**
     * Check if a specific attribute has been setted for this user
     * 
     * @param string $attribute
     *   Attribute name
     * 
     * @return bool
     *   True if the user has the requested attribute. False otherwise
     */
    public function hasAttribute(string $attribute): bool;
    
    /**
     * Get all user's roles
     * 
     * @return array
     *   All roles
     */
    public function getRoles(): array;
    
    /**
     * Check if the user has a specific role
     * 
     * @param string $role
     *   True if the user has the request role. False otherwise
     *   
     * @return bool
     *   True if the user has the requested role. False otherwise
     */
    public function hasRole(string $role): bool;
    
}
