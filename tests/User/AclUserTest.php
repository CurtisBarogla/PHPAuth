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

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\Exception\ResourceNotFoundException;
use Zoe\Component\Security\Acl\Mask\MaskCollection;

/**
 * Common class for AclUser testing
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class AclUserTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\MutableAclUser::grant()
     */
    public function testGrant(): void
    {
        $resource = new Resource("foo", ResourceInterface::WHITELIST_BEHAVIOUR);
        $resource->addPermission("foo");
        $resource->addPermission("bar");
        $user = $this->getUser();
        if($user instanceof StorableAclUser) {
            $permissions = new MaskCollection("ACL_PERMISSIONS");
            $permissions->add($this->getMockedMask("foo", 0x0000));
            
            $user->setPermissions($permissions);
        }
        
        $this->assertNull($user->grant($resource, ["foo", "bar"]));
    }
    
    /**
     * @see \Zoe\Component\Security\User\MutableAclUser::deny()
     */
    public function testDeny(): void
    {
        $resource = new Resource("foo", ResourceInterface::WHITELIST_BEHAVIOUR);
        $resource->addPermission("foo");
        $resource->addPermission("bar");
        $user = $this->getUser();
        if($user instanceof StorableAclUser) {
            $permissions = new MaskCollection("ACL_PERMISSIONS");
            $permissions->add($this->getMockedMask("foo", 0x0003));
            
            $user->setPermissions($permissions);
        }
        
        $this->assertNull($user->deny($resource, ["foo", "bar"]));
    }
    
    /**
     * @see \Zoe\Component\Security\User\MutableAclUser::getPermission()
     */
    public function testGetPermission(): void
    {
        $setPermissions = function(ResourceInterface $resource, array $placeholderPermissions): void {
            foreach ($placeholderPermissions as $placeholderPermission) {
                $resource->addPermission($placeholderPermission);
            }
        };
        $resourceB = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        $resourceW = new Resource("bar", ResourceInterface::WHITELIST_BEHAVIOUR);
        $setPermissions($resourceB, ["foo", "bar"]);
        $setPermissions($resourceW, ["foo", "bar"]);
        
        $user = $this->getUser();
        $user->grant($resourceW, ["foo", "bar"]);
        $user->deny($resourceB, ["bar"]);
        
        $this->assertSame(0x0001, $user->getPermission("foo")->getValue());
        $this->assertSame(0x0003, $user->getPermission("bar")->getValue());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\MutableAclUser::getPermission()
     */
    public function testExceptionWhenGettingPermissionFromNonRegisteredResource(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage("This resource 'mop' is not registered");
        
        $user = $this->getUser();
        $user->getPermission("mop");
    }
    
    /**
     * Get an instance of AclUserInterface implementation tested
     * 
     * @return AclUserInterface
     *   AclUser instance
     */
    abstract protected function getUser(): AclUserInterface;
    
}
