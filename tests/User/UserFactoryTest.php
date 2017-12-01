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

namespace ZoeTest\Component\Security\User;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\MaskCollectionMock;
use ZoeTest\Component\Security\Mock\MaskMock;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\User\StorableAclUser;
use Zoe\Component\Security\User\StorableUser;
use Zoe\Component\Security\User\UserFactory;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;

/**
 * UserFactory testcase
 * 
 * @see \Zoe\Component\Security\User\UserFactory
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserFactoryTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createCredentialUser()
     */
    public function testCreateCredentialUser(): void
    {
        $user = 
            UserMock::initMock(MutableUserInterface::class, "Foo")
                ->mockGetName($this->once())
                ->mockIsRoot($this->once(), false)
                ->mockGetRoles($this->once(), ["Foo", "Bar"])
                ->mockGetAttributes($this->once(), ["Foo" => "Bar", "Bar" => "Foo"])
            ->finalizeMock();

        $credentialUser = UserFactory::createCredentialUser($user, "Foo", ["Foo" => "Bar", "Bar" => "Foo"]);
        
        $this->assertInstanceOf(CredentialUserInterface::class, $credentialUser);
        $this->assertInstanceOf(MutableUserInterface::class, $credentialUser);
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $credentialUser->getRoles());
        $this->assertFalse($credentialUser->isRoot());
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $credentialUser->getAttributes());
        $this->assertSame("Foo", $credentialUser->getPassword());
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo", "password" => "Foo"], $credentialUser->getCredentials());
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUser()
     */
    public function testCreateStorableUserFromMutable(): void
    {
        $user = 
            UserMock::initMock(MutableUserInterface::class, "Foo")
                ->mockGetName($this->once())
                ->mockIsRoot($this->once(), false)
                ->mockGetRoles($this->once(), ["Foo", "Bar"])
                ->mockGetAttributes($this->once(), ["Foo" => "Bar", "Bar" => "Foo"])
            ->finalizeMock();

        $storableUser = UserFactory::createStorableUser($user);
        
        $this->assertInstanceOf(StorableUserInterface::class, $storableUser);
        $this->assertFalse($storableUser instanceof MutableUserInterface);
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $storableUser->getRoles());
        $this->assertFalse($storableUser->isRoot());
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $storableUser->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUser()
     */
    public function testCreateStorableUserFromCredential(): void
    {
        $user = 
            UserMock::initMock(CredentialUserInterface::class, "Foo")
                ->mockGetName($this->once())
                ->mockIsRoot($this->once(), false)
                ->mockGetRoles($this->once(), ["Foo", "Bar"])
                ->mockGetAttributes($this->once(), ["Foo" => "Bar", "Bar" => "Foo"])
            ->finalizeMock();
        
        $storableUser = UserFactory::createStorableUser($user);
        
        $this->assertInstanceOf(StorableUserInterface::class, $storableUser);
        $this->assertFalse($storableUser instanceof MutableUserInterface);
        $this->assertFalse($storableUser instanceof CredentialUserInterface);
        $this->assertSame("Foo", $storableUser->getName());
        $this->assertFalse($storableUser->isRoot());
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $storableUser->getRoles());
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $storableUser->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUser()
     */
    public function testCreateStorableWithAclAttribute(): void
    {
        $mask = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->mockGetValue($this->once(), 3)->finalizeMock();
        $permissions = MaskCollectionMock::initMock()->mockGet($this->any(), "Foo", $mask)->finalizeMock();
        $user = 
                UserMock::initMock(MutableUserInterface::class, "Foo")
                    ->mockHasAttribute($this->once(), AclUserInterface::ACL_ATTRIBUTES_IDENTIFIER, true)
                    ->mockGetAttribute($this->once(), AclUserInterface::ACL_ATTRIBUTES_IDENTIFIER, $permissions)
                    ->mockDeleteAttribute($this->once(), AclUserInterface::ACL_ATTRIBUTES_IDENTIFIER)
                    ->mockGetName($this->atLeastOnce())
                    ->mockIsRoot($this->atLeastOnce(), false)
                    ->mockGetRoles($this->atLeastOnce(), ["Foo", "Bar"])
                    ->mockGetAttributes($this->atLeastOnce(), ["Foo" => "Bar", "Bar" => "Foo"])
                ->finalizeMock();

        $storable = UserFactory::createStorableUser($user);
        
        $this->assertInstanceOf(StorableUserInterface::class, $storable);
        $this->assertInstanceOf(AclUserInterface::class, $storable);
        $this->assertSame(3, $storable->getPermission("Foo")->getValue());
        $this->assertFalse($storable->hasAttribute(AclUserInterface::ACL_ATTRIBUTES_IDENTIFIER));
        $this->assertSame("Foo", $storable->getName());
        $this->assertFalse($storable->isRoot());
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $storable->getRoles());
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $storable->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUserFromJson()
     */
    public function testCreateStorableUserFromJson(): void
    {
        $user = new StorableUser("foo", true, ["foo"], ["foo" => "bar"]);
        $json = \json_encode($user);
        
        $this->assertEquals($user, UserFactory::createStorableUserFromJson($json));
    }
    
    /**
     * @see \Zoe\Component\Security\User\UserFactory::createStorableUserFromJson()
     */
    public function testCreateStorableAclUserFromJson(): void
    {
        $permissions = new MaskCollection("foo");
        $permissions->add(new Mask("foo", 0x0001));
        $permissions->add(new Mask("bar", 0x000F));
        
        $user = new StorableAclUser("foo");
        $user->setPermissions($permissions);
        
        $json = \json_encode($user);
        
        $this->assertEquals($user, UserFactory::createStorableUserFromJson($json));
    }
    
}
