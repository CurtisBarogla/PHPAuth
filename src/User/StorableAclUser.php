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

use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\User\Contracts\AclUserInterface;

/**
 * Storable AclUser
 * Permissions stay mutable
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class StorableAclUser extends StorableUser implements AclUserInterface
{
    
    use AclUserTrait;
    
    /**
     * User permissions
     * 
     * @var MaskCollection
     */
    protected $permissions;

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
        parent::__construct($name, $isRoot, $roles, $attributes);
        $this->permissions = new MaskCollection("ACL_PERMISSIONS");
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\AclUserInterface::grant()
     */
    public function grant(ResourceInterface $resource, array $permissions): void
    {
        $this->apply($resource, $permissions, "add");
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Contracts\AclUserInterface::deny()
     */
    public function deny(ResourceInterface $resource, array $permissions): void
    {
        $this->apply($resource, $permissions, "sub");
    }

    /**
     * Set user permissions.
     * Should not be called by end-user
     * 
     * @param MaskCollection $permissions
     *   Permissions to set
     */
    public function setPermissions(MaskCollection $permissions): void
    {
        $this->permissions = $permissions;
    }
    
    /**
     * {@inheritdoc}
     * @see \Zoe\Component\Security\User\AclUserTrait::getPermissionContainer()
     */
    protected function getPermissionsContainer(): MaskCollection
    {
        return $this->permissions;
    }

}
