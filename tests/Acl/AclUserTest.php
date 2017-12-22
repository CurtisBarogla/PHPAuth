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
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Internal\ReflectionTrait;
use Zoe\Component\Security\Acl\AclUser;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use Zoe\Component\Security\User\AuthenticatedUserInterface;

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
        $fooPermission = MaskMock::init("FooPermission")->finalizeMock();
        $barMozPermission = MaskMock::init("BarMozPermission")->finalizeMock();
        $userPermission = MaskMock::init("UserPermission")
                                        ->mockAdd_consecutive(
                                            $this->exactly(2), 
                                            [[$fooPermission], [$barMozPermission]])
                                    ->finalizeMock();
        $fooEntity = EntityMock::init("FooEntity")
                                    ->mockGet($this->once(), "BAR", null)            
                                ->finalizeMock();
        $barEntity = EntityMock::init("BarEntity")
                                    ->mockGet($this->once(), "BAR", ["Bar", "Moz"])
                                ->finalizeMock();
        $collection = MaskCollectionMock::init("BarMozPermissionCollection")
                                            ->mockTotal($this->once(), null, null, $barMozPermission)
                                        ->finalizeMock();
        $resource = ResourceMock::init("FooResource")
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["BAR"]], 
                                        null,
                                        $fooPermission, null)
                                    ->mockGetEntities($this->once(), [$fooEntity, $barEntity])
                                    ->mockGetPermissions($this->once(), ["Bar", "Moz"], $collection)
                                ->finalizeMock();
        
        $user = new AclUser($userPermission, $this->getMockAuthenticatedUser());
        $this->assertNull($user->grant($resource, ["Foo", "BAR"]));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::deny()
     */
    public function testDeny(): void
    {
        $fooPermission = MaskMock::init("FooPermission")->finalizeMock();
        $barMozPermission = MaskMock::init("BarMozPermission")->finalizeMock();
        $userPermission = MaskMock::init("UserPermission")
                                        ->mockSub_consecutive(
                                            $this->exactly(2),
                                            [[$fooPermission], [$barMozPermission]])
                                    ->finalizeMock();
        $fooEntity = EntityMock::init("FooEntity")
                                    ->mockGet($this->once(), "BAR", null)
                                ->finalizeMock();
        $barEntity = EntityMock::init("BarEntity")
                                    ->mockGet($this->once(), "BAR", ["Bar", "Moz"])
                                ->finalizeMock();
        $collection = MaskCollectionMock::init("BarMozPermissionCollection")
                                            ->mockTotal($this->once(), null, null, $barMozPermission)
                                        ->finalizeMock();
        $resource = ResourceMock::init("FooResource")
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2),
                                        [["Foo"], ["BAR"]],
                                        null,
                                        $fooPermission, null)
                                    ->mockGetEntities($this->once(), [$fooEntity, $barEntity])
                                    ->mockGetPermissions($this->once(), ["Bar", "Moz"], $collection)
                                ->finalizeMock();
                
        $user = new AclUser($userPermission, $this->getMockAuthenticatedUser());
        $this->assertNull($user->deny($resource, ["Foo", "BAR"]));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::getPermissions()
     */
    public function testGetPermissions(): void
    {
        $permissions = $this->getMockMaskPermission(0);
        
        $user = new AclUser($permissions, $this->getMockAuthenticatedUser());
        
        $this->assertSame($permissions, $user->getPermissions());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::grant()
     */
    public function testExceptionGrantWhenAPermissionIsNotValid(): void
    {
        $fooPermission = MaskMock::init("FooPermission")->finalizeMock();
        $userPermission = MaskMock::init("UserPermission")->mockAdd($this->once(), $fooPermission)->finalizeMock();
        $fooEntity = EntityMock::init("FooEntity")->mockGet($this->once(), "BAR", null)->finalizeMock();
        $barEntity = EntityMock::init("BarEntity")->mockGet($this->once(), "BAR", null)->finalizeMock();

        $resource = ResourceMock::init("ResourceFoo")
                                    ->mockGetName($this->once(), "FooResource")
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["BAR"]],
                                        null,
                                        $fooPermission, null)
                                    ->mockGetEntities($this->once(), [$fooEntity, $barEntity])
                                ->finalizeMock();
        
                                
        $this->expectException(InvalidPermissionException::class);
        $this->expectExceptionMessage("This permissions 'Foo, BAR' cannot be granted as 'BAR' is not setted as a permission nor as an entity value for 'FooResource' resource for user 'Foo'");
        $user = new AclUser($userPermission, $this->getMockAuthenticatedUser());
        $user->grant($resource, ["Foo", "BAR"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::deny()
     */
    public function testExceptionDenyWhenAPermissionIsNotValid(): void
    {
        $userPermission = MaskMock::init("UserPermission")->mockSub($this->once(), MaskMock::init("Placeholder")->finalizeMock())->finalizeMock();
        $fooPermission = MaskMock::init("FooPermission")->finalizeMock();
        $fooEntity = EntityMock::init("FooEntity")->mockGet($this->once(), "BAR", null)->finalizeMock();
        $barEntity = EntityMock::init("BarEntity")->mockGet($this->once(), "BAR", null)->finalizeMock();
        
        $resource = ResourceMock::init("ResourceFoo")
                                    ->mockGetName($this->once(), "FooResource")
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2),
                                        [["Foo"], ["BAR"]],
                                        null,
                                        $fooPermission, null)
                                    ->mockGetEntities($this->once(), [$fooEntity, $barEntity])
                                ->finalizeMock();
            
            
        $this->expectException(InvalidPermissionException::class);
        $this->expectExceptionMessage("This permissions 'Foo, BAR' cannot be denied as 'BAR' is not setted as a permission nor as an entity value for 'FooResource' resource for user 'Foo'");
        $user = new AclUser($userPermission, $this->getMockAuthenticatedUser());
        $user->deny($resource, ["Foo", "BAR"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::grant()
     */
    public function testExceptionGrantWhenAPermissionIsNotValidAndNoEntityAreRegistered(): void
    {
        $this->expectException(InvalidPermissionException::class);
        $this->expectExceptionMessage("Permission 'Moz' not found into resource 'Bar' for user 'Foo'");
        $resource = ResourceMock::init("ResourceNoEntityGrant")
                                    ->mockGetPermission($this->once(), "Moz", null)
                                    ->mockGetName($this->once(), "Bar")
                                ->finalizeMock();
        
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        $user->grant($resource, ["Moz"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\AclUser::deny()
     */
    public function testExceptionDenyWhenAPermissionIsNotValidAndNoEntityAreRegistered(): void
    {
        $this->expectException(InvalidPermissionException::class);
        $this->expectExceptionMessage("Permission 'Moz' not found into resource 'Bar' for user 'Foo'");
        $resource = ResourceMock::init("ResourceNoEntityDeny")
                                    ->mockGetPermission($this->once(), "Moz", null)
                                    ->mockGetName($this->once(), "Bar")
                                ->finalizeMock();
        
        $user = new AclUser($this->getMockMaskPermission(0), $this->getMockAuthenticatedUser());
        $user->deny($resource, ["Moz"]);
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
                            ->mockGetName($this->exactly(5), "Foo")
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
