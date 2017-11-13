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
 * Basic user implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
final class User implements UserInterface
{
    
    /**
     * User identifier
     * 
     * @var string
     */
    private $name;
    
    /**
     * User password
     * 
     * @var string
     */
    private $password;
    
    /**
     * User extra attributes
     * 
     * @var array|null
     */
    private $attributes = null;
    
    /**
     * Roles 
     * 
     * @var array
     */
    private $roles = [];
    
    /**
     * Is root
     * 
     * @var bool
     */
    private $isRoot = false;
    
    /**
     * Initialize a basic user
     * 
     * @param string $name
     *   User name
     * @param string|null $password
     *   User password
     * @param array $roles
     *   User roles
     * @param bool $isRoot
     *   If the user is root
     * @param array|null $attributes
     *   User attributes
     */
    public function __construct(string $name, ?string $password, array $roles = [], bool $isRoot = false, ?array $attributes = null)
    {
        $this->name = $name;
        $this->password = $password;
        $this->roles = $roles;
        $this->isRoot = $isRoot;
        $this->attributes = $attributes;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getName()
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getPassword()
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set a new role for the user
     * 
     * @param string $role
     *   Role to add
     * 
     * @return self
     *   self
     */
    public function addRole(string $role): self
    {
        $this->roles[$role] = $role;
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getRoles()
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::hasRole()
     */
    public function hasRole(string $role): bool
    {
        return isset($this->roles[$role]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::isRoot()
     */
    public function isRoot(): bool
    {
        return $this->isRoot;
    }
    
    /**
     * Add an extra attribute to the user
     * 
     * @param string $name
     *   Attribute name
     * @param mixed $value
     *   Attribute value
     * 
     * @return self
     *   self 
     */
    public function addAttribute(string $name, $value): self
    {
        $this->attributes[$name] = $value;
        
        return $this;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getAttributes()
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getAttribute()
     */
    public function getAttribute(string $name)
    {
        if(!isset($this->attributes[$name]))
            throw new InvalidUserAttributeException(\sprintf("This attribute '%s' for user '%s' is not setted",
                $name,
                $this->name));
            
        return $this->attributes[$name];
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::hasAttribute()
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
    
    /**
     * Output user name
     * 
     * @return string
     *   User name
     */
    public function __toString(): string
    {
        return $this->name;
    }

}
