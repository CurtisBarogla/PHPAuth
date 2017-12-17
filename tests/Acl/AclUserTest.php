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

namespace ZoeTest\Component\Security\Acl;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\User\AuthenticatedUserInterface;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\Acl\AclUser;
use Zoe\Component\Security\Acl\Mask\Mask;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use Zoe\Component\Security\Acl\Resource\ImmutableResourceInterface;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use Zoe\Component\Internal\ReflectionTrait;

/**
 * AclUser testcase
 * 
 * @see \Zoe\Component\Security\Acl\AclUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AclUserTest extends TestCase
{
    
    use ReflectionTrait;
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::getName()
     */
    public function testGetName(): void
    {
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $this->assertSame("Foo", $user->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::isRoot()
     */
    public function testIsRoot(): void
    {
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $this->assertTrue($user->isRoot());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::addAttribute()
     */
    public function testAddAttribute(): void
    {
        $this->expectException(\BadMethodCallException::class);
        
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        $user->addAttribute("Foo", "Bar");
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::getAttributes()
     */
    public function testGetAttributes(): void
    {
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $user->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::getAttribute()
     */
    public function testGetAttribute(): void
    {
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $this->assertSame("Bar", $user->getAttribute("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::hasAttribute()
     */
    public function testHasAttribute(): void
    {
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $this->assertTrue($user->hasAttribute("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::deleteAttribute()
     */
    public function testDeleteAttribute(): void
    {
        $this->expectException(\BadMethodCallException::class);
        
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        $user->deleteAttribute("Foo");
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::getRoles()
     */
    public function testGetRoles(): void
    {
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $user->getRoles());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::hasRole()
     */
    public function testHasRole(): void
    {
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $this->assertTrue($user->hasRole("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::authenticatedAt()
     */
    public function testAuthenticatedAt(): void
    {
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $userDate = $user->authenticatedAt();
        
        $this->assertSame((new \DateTime())->format("d/m/Y H:i:s"), $userDate->format("d/m/Y H:i:s"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::grant()
     */
    public function testGrant(): void
    {
        $permission = MaskMock::init("PermissionMask")
                                ->mockGetValue($this->once(), 5)
                            ->finalizeMock();
        $permissionUserMask = MaskMock::init("UserPermissionMask")
                                        ->mockAdd($this->once(), $permission)
                                    ->finalizeMock();
        $permissions = MaskCollectionMock::init("ResourceGrantTestPermissions")
                                            ->mockTotal($this->once(), null, null, $permission)
                                        ->finalizeMock();
        $resource = ResourceMock::init("ResourceGrantTest", ImmutableResourceInterface::class)
                                    ->mockGetPermissions($this->once(), ["Foo", "Bar"], $permissions)
                                ->finalizeMock();
        
        $user = new AclUser($permissionUserMask, $this->getMockAuthenticatedUser());
        $this->assertNull($user->grant($resource, ["Foo", "Bar"]));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::deny()
     */
    public function testDeny(): void
    {
        $permission = MaskMock::init("PermissionMask")
                                ->mockGetValue($this->once(), 5)
                            ->finalizeMock();
        $permissionUserMask = MaskMock::init("UserPermissionMask")
                                        ->mockSub($this->once(), $permission)
                                    ->finalizeMock();
        $permissions = MaskCollectionMock::init("ResourceGrantTestPermissions")
                                            ->mockTotal($this->once(), null, null, $permission)
                                        ->finalizeMock();
        $resource = ResourceMock::init("ResourceGrantTest", ImmutableResourceInterface::class)
                                    ->mockGetPermissions($this->once(), ["Foo", "Bar"], $permissions)
                                ->finalizeMock();
        
        $user = new AclUser($permissionUserMask, $this->getMockAuthenticatedUser());
        $this->assertNull($user->deny($resource, ["Foo", "Bar"]));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::grant()
     */
    public function testExceptionGrantWhenAPermissionIsNotValid(): void
    {
        $this->expectException(InvalidPermissionException::class);
        $this->expectExceptionMessage("Cannot grant this permission 'Bar' as it is not declared into the resource 'Foo'");
        
        $reflection = new \ReflectionClass(ImmutableResourceInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $exception = new InvalidPermissionException();
        $exception->setInvalidPermission("Bar");
        $resource = $this->getMockBuilder(ImmutableResourceInterface::class)->setMethods($methods)->getMock();
        $resource->expects($this->once())->method("getPermissions")->with(["Foo", "Bar"])->will($this->throwException($exception));
        $resource->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        $user->grant($resource, ["Foo", "Bar"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::deny()
     */
    public function testExceptionDenyWhenAPermissionIsNotValid(): void
    {
        $this->expectException(InvalidPermissionException::class);
        $this->expectExceptionMessage("Cannot deny this permission 'Bar' as it is not declared into the resource 'Foo'");
        
        $reflection = new \ReflectionClass(ImmutableResourceInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $exception = new InvalidPermissionException();
        $exception->setInvalidPermission("Bar");
        $resource = $this->getMockBuilder(ImmutableResourceInterface::class)->setMethods($methods)->getMock();
        $resource->expects($this->once())->method("getPermissions")->with(["Foo", "Bar"])->will($this->throwException($exception));
        $resource->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        $user->deny($resource, ["Foo", "Bar"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::jsonSerialize()
     */
    public function testExceptionWhenJsonEncodeAclUser(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("Acl user cannot be jsonified");
        
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        
        $json = \json_encode($user);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::restoreFromJson()
     */
    public function testExceptionWhenRestoreFromJsonAclUser(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("Acl user cannot be restored from a json representation");
        
        AclUser::restoreFromJson("Foo");
    }
    
    /**
     * Get a fully mocked mask permission
     * 
     * @param int $value
     *   Value to set into the mask
     * 
     * @return Mask
     *   Mocked mask permission
     */
    private function getMockMaskPermission(int $value): Mask
    {
        return MaskMock::init("MaskPermissionFromAclWrappedUser")
                            ->mockGetIdentifier($this->any(), "PERMISSIONS")
                            ->mockGetValue($this->any(), $value)
                        ->finalizeMock();
    }
    
    /**
     * Get a fully mocked AuthenticatedUser
     * 
     * @return AuthenticatedUserInterface
     *   Mocked authenticated user
     */
    private function getMockAuthenticatedUser(): AuthenticatedUserInterface
    {
        return UserMock::init("AclWrappedAuthenticatedUser", AuthenticatedUserInterface::class)
                            // 3 times (getName() test + 2 times exception)
                            ->mockGetName($this->exactly(3), "Foo")
                            ->mockIsRoot($this->once(), true)
                            ->mockAddAttribute($this->never(), "Foo", "Bar")
                            ->mockGetAttributes($this->once(), ["Foo" => "Bar", "Bar" => "Foo"])
                            ->mockGetAttribute($this->once(), "Foo", "Bar")
                            ->mockHasAttribute($this->once(), "Foo", true)
                            ->mockDeleteAttribute($this->never(), "Foo", false)
                            ->mockGetRoles($this->once(), ["Foo", "Bar"])
                            ->mockHasRole($this->once(), "Foo", true)
                            ->mockAuthenticatedAt($this->once(), new \DateTime())
                        ->finalizeMock();
                            
    }
    
}
