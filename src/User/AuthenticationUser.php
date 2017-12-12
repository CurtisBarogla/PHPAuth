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

use Zoe\Component\Security\Exception\User\InvalidUserCredentialException;
use Zoe\Component\Security\Exception\User\InvalidUserRoleException;

/**
 * AuthenticationUser is meant to be processed by authentication process.
 * Credentials and role mutability will not be persisted after this process 
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationUser extends User implements AuthenticationUserInterface
{
    
    /**
     * User's credentials
     * 
     * @var array
     */
    private $credentials = [];
    
    /**
     * Initialize an authentication user.
     * AuthenticationUser is fully mutable during the authentication process
     * 
     * @param string $name
     *   User name
     * @param string|null $password
     *   User password
     * @param array $attributes
     *   Defaults user's attributes
     * @param string[] $roles
     *   Defaults user's roles
     * @param array $credentials
     *   Defaults user's credentials
     */
    public function __construct(string $name, ?string $password = null, array $attributes = [], array $roles = [], array $credentials = [])
    {
        parent::__construct($name, $attributes, $roles);
        $this->credentials = $credentials;
        $this->credentials["USER_PASSWORD"] = $password;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::changeName()
     */
    public function changeName(string $name): void
    {
        $this->name = $name;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::getPassword()
     */
    public function getPassword(): ?string
    {
        return $this->credentials["USER_PASSWORD"] ?? null;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::addRole()
     */
    public function addRole(string $role): void
    {
        $this->roles[$role] = $role;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::deleteRole()
     */
    public function deleteRole(string $role): void
    {
        if(!isset($this->roles[$role]))
            throw new InvalidUserRoleException($this, $role);
        
        unset($this->roles[$role]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::addCredential()
     */
    public function addCredential(string $credential, $value): void
    {
        $this->credentials[$credential] = $value;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::getCredentials()
     */
    public function getCredentials(): array
    {
        return $this->credentials;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::getCredential()
     */
    public function getCredential(string $credential)
    {
        if(!isset($this->credentials[$credential]))
            throw new InvalidUserCredentialException($this, $credential);
        
        return $this->credentials[$credential];
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::hasCredential()
     */
    public function hasCredential(string $credential): bool
    {
        return isset($this->credentials[$credential]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::deleteCredential()
     */
    public function deleteCredential(string $credential): void
    {
        if(!isset($this->credentials[$credential]))
            throw new InvalidUserCredentialException($this, $credential);
        
        unset($this->credentials[$credential]);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticationUserInterface::deleteCredentials()
     */
    public function deleteCredentials(): void
    {
        unset($this->credentials);
        $this->credentials = [];
    }

}
