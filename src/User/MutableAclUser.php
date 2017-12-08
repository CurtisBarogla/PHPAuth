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
 * Mutable AclUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MutableAclUser extends MutableUser implements AclUserInterface
{
    
    use AclUserTrait;
    
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
        $this->addAttribute(AclUserInterface::ACL_ATTRIBUTES_IDENTIFIER, new MaskCollection("ACL_PERMISSIONS"));
    }
    
    /**
     * {@inheritdoc}
     * @see \Zoe\Component\Security\User\AclUserTrait::getPermissionContainer()
     */
    protected function getPermissionsContainer(): MaskCollection
    {
        return $this->attributes[AclUserInterface::ACL_ATTRIBUTES_IDENTIFIER];
    }

}
