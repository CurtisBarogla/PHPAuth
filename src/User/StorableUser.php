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
 * Basic user setted into a storage
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class StorableUser implements StorableUserInterface
{
    
    /**
     * User identifier
     * 
     * @var string
     */
    private $name;
    
    /**
     * User attributes
     * 
     * @var array|null
     */
    private $attributes;
    
    /**
     * Roles user
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
     * Initialize a storable user
     * 
     * @param string $name
     *   User name
     * @param array $roles
     *   Roles user
     * @param array|null $attributes
     *   User attributes
     */
    public function __construct(string $name, array $roles, ?array $attributes)
    {
        $this->name = $name;
        $this->roles = $roles;
        $this->attributes = $attributes;
    }
    
    /**
     * Create a storable user from a user instance
     * 
     * @param UserInterface $user
     *   User instance
     * 
     * @return StorableUserInterface
     *   Storable user
     */
    public static function createFromUser(UserInterface $user): StorableUserInterface
    {
        $storable = new StorableUser($user->getName(), $user->getRoles(), $user->getAttributes());
        if($user->isRoot()) 
            $storable->setRoot();
        unset($user);
        
        return $storable;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\StorableUserInterface::getName()
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\StorableUserInterface::isRoot()
     */
    public function isRoot(): bool
    {
        return $this->isRoot;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\StorableUserInterface::getRoles()
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\StorableUserInterface::getAttributes()
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }
    
    /**
     * Set the stored user as root
     */
    private function setRoot(): void
    {
        $this->isRoot = true;
    }

}
