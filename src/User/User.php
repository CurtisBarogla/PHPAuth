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
 * Common class for all UserInterface implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class User implements UserInterface
{
    
    /**
     * Username
     * 
     * @var string
     */
    protected $name;
    
    /**
     * Root user
     * 
     * @var bool
     */
    protected $root;
    
    /**
     * User's attributes
     * 
     * @var array
     */
    protected $attributes = [];
    
    /**
     * User's roles
     * 
     * @var array
     */
    protected $roles = [];
    
    /**
     * Initialize user
     * 
     * @param string $name
     *   Username
     * @param bool $root
     *   Root user
     * @param array $attributes
     *   Defaults user's attributes
     * @param string[] $roles
     *   Defaults user's roles
     */
    public function __construct(string $name, bool $root = false, array $attributes = [], array $roles = [])
    {
        $this->name = $name;
        $this->root = $root;
        $this->attributes = $attributes;
        $this->roles = \array_combine($roles, $roles);
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
     * @see \Zoe\Component\Security\User\AuthenticatedUserInterface::isRoot()
     */
    public function isRoot(): bool
    {
        return $this->root;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::addAttribute()
     */
    public function addAttribute(string $attribute, $value): void
    {
        $this->attributes[$attribute] = $value;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getAttributes()
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getAttribute()
     */
    public function getAttribute(string $attribute)
    {
        if(!isset($this->attributes[$attribute]))
            throw new InvalidUserAttributeException($this, $attribute);
        
        return $this->attributes[$attribute];
    }


    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::hasAttribute()
     */
    public function hasAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
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

}
