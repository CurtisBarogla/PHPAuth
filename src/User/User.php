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
use Zoe\Component\Security\User\Contracts\UserInterface;

/**
 * Common class for all users
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class User implements UserInterface
{
    
    /**
     * User identifier
     * 
     * @var string
     */
    protected $name;
    
    /**
     * Role associate the user
     * 
     * @var array
     */
    protected $roles = [];
    
    /**
     * User attributes
     * 
     * @var array|null
     */
    protected $attributes = null;
    
    /**
     * If the user is considered root
     * 
     * @var bool
     */
    protected $isRoot = false;
    
    /**
     * Initialize a user
     * 
     * @param string $name
     *   User name
     * @param bool $isRoot
     *   If the user is considered root
     * @param array|null $roles
     *   Roles
     * @param array|null $attributes
     *   Default attributes
     */
    public function __construct(
        string $name,
        bool $isRoot = false,
        ?array $roles = null,
        ?array $attributes = null)
    {
        $this->name = $name;
        $this->isRoot = $isRoot;
        $this->attributes = $attributes;
        if(null !== $roles)
            $this->roles = \array_combine($roles, $roles);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\UserInterface::getName()
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\UserInterface::isRoot()
     */
    public function isRoot(): bool
    {
        return $this->isRoot;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\UserInterface::getRoles()
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\UserInterface::getAttributes()
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\UserInterface::getAttribute()
     */
    public function getAttribute(string $attribute)
    {
        if(!isset($this->attributes[$attribute]))
            throw new InvalidUserAttributeException($this, $attribute);
        
        return $this->attributes[$attribute];
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\UserInterface::hasRole()
     */
    public function hasRole(string $role): bool
    {
        return isset($this->roles[$role]);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\UserInterface::hasAttribute()
     */
    public function hasAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\UserInterface::__toString()
     */
    public function __toString(): string
    {
        return $this->name;
    }

}