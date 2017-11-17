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

use Zoe\Component\Security\Exception\InvalidUserCredentialException;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;

/**
 * Basic credential user implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
final class CredentialUser extends MutableUser implements CredentialUserInterface
{
    
    /**
     * User credentials
     * 
     * @var array|null
     */
    private $credentials;
    
    /**
     * Initialize the user
     * 
     * @param string $name
     *   User name
     * @param string|null $password
     *   User password
     * @param bool $isRoot
     *   If the user is considered root
     * @param array|null $roles
     *   Roles
     * @param array|null $credentials
     *   User credentials
     * @param array|null $attributes
     *   Default attributes
     */
    public function __construct(
        string $name,
        ?string $password,
        bool $isRoot = false,
        ?array $roles = null, 
        ?array $credentials = null, 
        ?array $attributes = null)
    {
        parent::__construct($name, $isRoot, $roles, $attributes);
        $this->credentials = $credentials;
        if(null !== $password)
            $this->credentials["password"] = $password;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\CredentialUserInterface::getPassword()
     */
    public function getPassword(): ?string
    {
        if(!isset($this->credentials["password"])) return null;
        
        return $this->credentials["password"];
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\CredentialUserInterface::getCredentials()
     */
    public function getCredentials(): ?array
    {
        return $this->credentials;
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\CredentialUserInterface::getCredential()
     */
    public function getCredential(string $credential): string
    {
        if(!isset($this->credentials[$credential]))
            throw new InvalidUserCredentialException($this, $credential);
        
        return $this->credentials[$credential];
    }
        
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\CredentialUserInterface::hasCredential()
     */
    public function hasCredential(string $credential): bool
    {
        return isset($this->credentials[$credential]);
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\CredentialUserInterface::addCredential()
     */
    public function addCredential(string $credential, string $value): CredentialUserInterface
    {
        $this->credentials[$credential] = $value;
        
        return $this;
    }

}
