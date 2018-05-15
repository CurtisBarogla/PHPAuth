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
use Ness\Component\User\Exception\UserAttributeNotFoundException;
use Ness\Component\User\UserInterface;

/**
 * Basic implementation of the authenticated user.
 * This implementation will set root status based on an attribute.
 * This implementation does not allow AuthenticatedUser initialization other than from an already setted UserInterface
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
final class AuthenticatedUser extends User implements AuthenticatedUserInterface
{
    
    /**
     * Authenticated time
     * 
     * @var \DateTimeInterface
     */
    private $authenticatedAt;
    
    /**
     * Root status
     * 
     * @var bool
     */
    private $root = false;
    
    /**
     * Attribute needed to check root status of the user
     *
     * @var string
     */
    public const ROOT_ATTRIBUTE_IDENTIFIER = "is_root";
    
    /**
     * Make private
     * 
     * @param string $name
     *   User name
     * @param array|null $attributes
     *   User attributes
     * @param iterable|null $roles
     *   User roles
     */
    private function __construct(string $name, ?array $attributes, ?iterable $roles = null)
    {
        parent::__construct($name, $attributes, $roles);
        $this->authenticatedAt = new \DateTime();
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\User\AuthenticatedUserInterface::authenticatedAt()
     */
    public function authenticatedAt(): \DateTimeInterface
    {
        return $this->authenticatedAt;
    }

    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\User\AuthenticatedUserInterface::isRoot()
     */
    public function isRoot(): bool
    {
        return $this->root;
    }
    
    /**
     * Initialize an AuthenticatedUser
     * Purge root attribute when setted.
     * 
     * @param UserInterface $user
     *   User which to initialize
     * 
     * @return AuthenticationUserInterface
     *   Authentication user initialized
     * 
     * @throws \TypeError
     *   When given crendentials attribute is not a boolean
     */
    public static function initializeFromUser(UserInterface $user): AuthenticatedUserInterface
    {
        $user = new self($user->getName(), $user->getAttributes(), $user->getRoles());
        
        try {
            $root = $user->getAttribute(self::ROOT_ATTRIBUTE_IDENTIFIER);
            if(!\is_bool($root))
                throw new \TypeError(\sprintf("Root attribute MUST a boolean. '%s' given",
                    \gettype($root)));
                
            $user->root = $root;
            $user->deleteAttribute(self::ROOT_ATTRIBUTE_IDENTIFIER);
            
            return $user;
        } catch (UserAttributeNotFoundException $e) {
            return $user;
        }
    }
    
}
