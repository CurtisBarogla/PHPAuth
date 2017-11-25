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
use Zoe\Component\Security\User\Contracts\AclUserInterface;

/**
 * Storable AclUser
 * Permissions stay mutable
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class StorableAclUser extends StorableUser implements AclUserInterface, \JsonSerializable
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
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\StorableUser::jsonSerialize()
     */
    public function jsonSerialize(): array
    {
        $user = parent::jsonSerialize();
        
        return \array_merge($user, ["permissions" => $this->permissions]);
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
