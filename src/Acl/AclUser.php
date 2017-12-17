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

namespace Zoe\Component\Security\Acl;

use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Resource\ImmutableResourceInterface;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use Zoe\Component\Security\User\AuthenticatedUserInterface;

/**
 * AclUser interacts directly with an Acl.
 * AclUserInterface native implementation <br />
 * This implementation wrap an already authenticated user and act like a proxy over it <br />
 * This implementation deny all possibility of mutability over the wrapped user so the acl cannot alter it <br />
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AclUser implements AclUserInterface
{
    
    /**
     * Current permissions mask
     * 
     * @var Mask
     */
    private $permissions;
    
    /**
     * User authenticated
     * 
     * @var AuthenticatedUserInterface $user
     */
    private $user;
    
    /**
     * Initialize AclUser
     * 
     * @param Mask $permissions
     *   Defaults permissions mask
     * @param AuthenticatedUserInterface $user
     *   Wrapped user
     */
    public function __construct(Mask $permissions, AuthenticatedUserInterface $user)
    {
        $this->permissions = $permissions;
        $this->user = $user;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getName()
     */
    public function getName(): string
    {
        return $this->user->getName();
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::isRoot()
     */
    public function isRoot(): bool
    {
        return $this->user->isRoot();
    }
    
    /**
     * @throws \BadMethodCallException
     *   User is immutable
     * 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::addAttribute()
     */
    public function addAttribute(string $attribute, $value): void
    {
        throw new \BadMethodCallException(\sprintf("Cannot add an attribute on this user '%s' as it is in an immutable state",
            $this->user->getName()));
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getAttributes()
     */
    public function getAttributes(): array
    {
        return $this->user->getAttributes();
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getAttribute()
     */
    public function getAttribute(string $attribute)
    {
        return $this->user->getAttribute($attribute);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::hasAttribute()
     */
    public function hasAttribute(string $attribute): bool
    {
        return $this->user->hasAttribute($attribute);        
    }
    
    /**
     * @throws \BadMethodCallException
     *   User is immutable
     * 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::deleteAttribute()
     */
    public function deleteAttribute(string $attribute): void
    {
        throw new \BadMethodCallException(\sprintf("Cannot delete an attribute on this user '%s' as it is in an immutable state",
            $this->user->getName()));
    }
        
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::getRoles()
     */
    public function getRoles(): array
    {
        return $this->user->getRoles();
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\UserInterface::hasRole()
     */
    public function hasRole(string $role): bool
    {
        return $this->user->hasRole($role);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AuthenticatedUserInterface::authenticatedAt()
     */
    public function authenticatedAt(): \DateTimeInterface
    {
        return $this->user->authenticatedAt();
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\AclUserInterface::grant()
     */
    public function grant(ImmutableResourceInterface $resource, array $permissions): void
    {
        $this->set($resource, $permissions, "add");
    }

    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Acl\AclUserInterface::deny()
     */
    public function deny(ImmutableResourceInterface $resource, array $permissions): void
    {
        $this->set($resource, $permissions, "sub");
    }
    
    /**
     * {@inheritDoc}
     * @see \JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize(): void
    {
        throw new \BadMethodCallException(\sprintf("Acl user cannot be jsonified"));
    }
    
    /**
     * @throws \BadMethodCallException
     *   Cannot be called on acl user
     * 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Common\JsonSerializable
     */
    public static function restoreFromJson($json)
    {
        throw new \BadMethodCallException(\sprintf("Acl user cannot be restored from a json representation"));
    }
    
    /**
     * Proceed attribution
     * 
     * @param ImmutableResourceInterface $resource
     *   Resource 
     * @param array $permissions
     *   Permissions applied
     * @param string $method
     *   Method mask to execute
     * 
     * @throws InvalidPermissionException
     *   When error happen during attribute process
     */
    private function set(ImmutableResourceInterface $resource, array $permissions, string $method): void
    {
        try {
            $this->permissions->{$method}($resource->getPermissions($permissions)->total());
        } catch (InvalidPermissionException $e) {
            $method = ($method === "add") ? "grant" : "deny";
            throw new InvalidPermissionException(\sprintf("Cannot %s this permission '%s' as it is not declared into the resource '%s'",
                $method,
                $e->getInvalidPermission(),
                $resource->getName()));
        }
    }

}
