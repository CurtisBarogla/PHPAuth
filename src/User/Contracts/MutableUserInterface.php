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

/**
 * User which properties can still be altered
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface MutableUserInterface extends UserInterface
{
    
    /**
     * Add a role to the user
     * 
     * @param string $role
     *   Role name
     * 
     * @return self
     *   self
     */
    public function addRole(string $role): MutableUserInterface;
    
    /**
     * Add an attribute to the user
     * 
     * @param string $attribute
     *   Attribute name
     * @param mixed $value
     *   Attribute value
     * 
     * @return self
     *   self
     */
    public function addAttribute(string $attribute, $value): MutableUserInterface;
    
}
