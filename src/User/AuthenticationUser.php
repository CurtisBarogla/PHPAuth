<?php
//StrictType
declare(strict_types = 1);

/*
 * Ness
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
 
namespace Ness\Component\Authentication\User;

use Ness\Component\User\User;
use Ness\Component\User\UserInterface;
use Ness\Component\Authentication\Exception\UserCredentialNotFoundException;
use Ness\Component\Authentication\Exception\ImmutableUserException;

/**
 * Basic implementation of the authentication user.
 * This implementation will set credential based on an attribute.
 * This implementation does not allow AuthenticationUser initialization other than from an already setted UserInterface
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
final class AuthenticationUser extends User implements AuthenticationUserInterface
{
    
    /**
     * User's credentials
     * 
     * @var array
     */
    private $credentials = [];
    
    /**
     * Attribute needed to set credentials into user
     * 
     * @var string
     */
    public const CREDENTIAL_ATTRIBUTE_IDENTIFIER = "credentials";
    
    /**
     * Make private
     */
    private function __construct()
    {
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\User\User::addAttribute()
     * 
     * @throws ImmutableUserException
     *   Authentication user is immutable
     */
    public function addAttribute(string $attribute, $value): UserInterface
    {
        throw new ImmutableUserException("This user '{$this->getName()}' is in an immutable state. Therefore, no attribute can be setted");  
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\User\User::deleteAttribute()
     * 
     * @throws ImmutableUserException
     *   Authentication user is immutable
     */
    public function deleteAttribute(string $attribute): void
    {
        throw new ImmutableUserException("This user '{$this->getName()}' is in an immutable state. Therefore, no attribute can be removed");
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\User\AuthenticationUserInterface::getPassword()
     */
    public function getPassword(): string
    {
        return $this->getCredential("password");
    }

    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\User\AuthenticationUserInterface::getCredential()
     */
    public function getCredential(string $credential)
    {
        if(!\array_key_exists($credential, $this->credentials))
            throw new UserCredentialNotFoundException("This credential '{$credential}' is not setted for user '{$this->name}'");
        
        return $this->credentials[$credential];
    }

    /**
     * Initialize a new Authentication user from a basic user.
     * Purge credentials attribute when setted.
     * If no credential are found, will simply initialize a new AuthenticationUser with no credential setted
     * 
     * @param UserInterface $user
     *   User which to initialize
     * @param array|null $credentials
     *   Credentials to inject into the user
     * 
     * @return AuthenticationUserInterface
     *   Authentication user with credentials setted
     * 
     * @throws \TypeError
     *   When given crendentials attribute is not an array
     */
    public static function initializeFromUser(UserInterface $user, ?array $credentials = null): AuthenticationUserInterface
    {
        $instance = new self();
        $instance->name = $user->getName();
        $instance->attributes = $user->getAttributes();
        $instance->roles = $user->getRoles();
        
        if(null !== $credentials) {
            foreach ($credentials as $credential => $value)
                $instance->credentials[$credential] = $value;
        }
        
        if(null !== $credentials = $user->getAttribute(self::CREDENTIAL_ATTRIBUTE_IDENTIFIER)) {
            if(!\is_array($credentials))
                throw new \TypeError(\sprintf("Credentials attribute MUST be an array with each credential indexed by its name. '%s' given",
                    \gettype($credentials)));
            foreach ($credentials as $credential => $value)
                $instance->credentials[$credential] = $value;
            
            unset($instance->attributes[self::CREDENTIAL_ATTRIBUTE_IDENTIFIER]);
        }
        
        return $instance;
    }
    
}
