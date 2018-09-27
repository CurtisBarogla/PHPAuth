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
        $this->authenticatedAt = new \DateTimeImmutable();
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
     *   When given root attribute is not a boolean
     */
    public static function initializeFromUser(UserInterface $user): AuthenticatedUserInterface
    {
        $user = new self($user->getName(), $user->getAttributes(), $user->getRoles());
        
        if(null === $isRoot = $user->getAttribute(self::ROOT_ATTRIBUTE_IDENTIFIER))
            return $user;

        if(!\is_bool($isRoot))
            throw new \TypeError(\sprintf("Root attribute MUST a boolean. '%s' given",
                \gettype($isRoot)));
            
        $user->root = $isRoot;
        $user->deleteAttribute(self::ROOT_ATTRIBUTE_IDENTIFIER);
        
        return $user;
    }
    
}
