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

namespace ZoeTest\Component\Security\Mock\Tests;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\Exception\InvalidUserAttributeException;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Mask\Mask;

/**
 * UserMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\UserMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetName()
     */
    public function testMockGetName(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockGetName($this->any())->finalizeMock();
        $this->assertSame("Foo", $user->getName());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockIsRoot()
     */
    public function testMockIsRoot(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockIsRoot($this->any(), true)->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->mockIsRoot($this->any(), false)->finalizeMock();
        
        $this->assertTrue($user->isRoot());
        $this->assertFalse($user2->isRoot());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetRoles()
     */
    public function testMockGetRoles(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockGetRoles($this->any(), ["Foo", "Bar"])->finalizeMock();
        
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $user->getRoles());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetRoles_consecutive()
     */
    public function testMockGetRoles_consecutive(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")
                ->mockGetRoles_consecutive($this->exactly(3), ["Foo", "Bar", "Moz"], ["Foo", "Bar"], ["Foo"])->finalizeMock();
        
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar", "Moz" => "Moz"], $user->getRoles());
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $user->getRoles());
        $this->assertSame(["Foo" => "Foo"], $user->getRoles());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetAttributes()
     */
    public function testMockGetAttributes(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")
                    ->mockGetAttributes($this->any(), ["Foo" => "Bar", "Bar" => "Foo"])
                    ->finalizeMock();
        
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $user->getAttributes());
        
        $user = UserMock::initMock(UserInterface::class, "Foo")
                    ->mockGetAttributes($this->any(), null)
                    ->finalizeMock();
        
        $this->assertNull($user->getAttributes());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetAttributes_consecutive()
     */
    public function testMockGetAttributes_consecutive(): void
    {
        $attributes = [
            ["Foo" => "Bar", "Foo" => "Foo", "Moz" => "Poz"],
            ["Foo" => "Bar", "Foo" => "Foo"],
            ["Foo" => "Bar"]
        ];
        
        $user = UserMock::initMock(UserInterface::class, "Foo")
                    ->mockGetAttributes_consecutive($this->exactly(3), $attributes)->finalizeMock();
        
        $this->assertSame($attributes, $user->getAttributes());
        
        $attributes = [
            null, null, null
        ];
        
        $user = UserMock::initMock(UserInterface::class, "Foo")
            ->mockGetAttributes_consecutive($this->exactly(3), $attributes)->finalizeMock();
        
        $this->assertSame($attributes, $user->getAttributes());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetAttribute()
     */
    public function testMockGetAttribute(): void
    {
        // valid
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockGetAttribute($this->any(), "Foo", "Bar")->finalizeMock();
        
        $this->assertSame("Bar", $user->getAttribute("Foo"));
        
        //invalid
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockGetAttribute($this->any(), "Foo", new \Exception())->finalizeMock();
        $this->expectException(\Exception::class);
        $user->getAttribute("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetAttribute_consecutive()
     */
    public function testMockGetAttribute_consecutive(): void
    {
        // valid
        $user = UserMock::initMock(UserInterface::class, "Foo")
                    ->mockGetAttribute_consecutive($this->exactly(3), ["Foo" => "Bar", "Bar" => "Foo", "Moz" => "Poz"])
                    ->finalizeMock();
        
        $this->assertSame("Bar", $user->getAttribute("Foo"));
        $this->assertSame("Foo", $user->getAttribute("Bar"));
        $this->assertSame("Poz", $user->getAttribute("Moz"));
        
        // invalid
        $user = UserMock::initMock(UserInterface::class, "Foo")
                    ->mockGetAttribute_consecutive($this->exactly(2), ["Foo" => "Bar", "Bar" => new \Exception(), "Moz" => "Poz"])
                    ->finalizeMock();
        $this->expectException(\Exception::class);
        
        $user->getAttribute("Foo");
        $user->getAttribute("Bar");
        $user->getAttribute("Moz");
        
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockHasRole()
     */
    public function testMockHasRole(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockHasRole($this->any(), "Foo", true)->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->mockHasRole($this->any(), "Foo", false)->finalizeMock();
        
        $this->assertTrue($user->hasRole("Foo"));
        $this->assertFalse($user2->hasRole("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockHasRole_consecutive()
     */
    public function testMockHasRole_consecutive(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")
                    ->mockHasRole_consecutive($this->exactly(3), ["Foo" => true, "Bar" => false, "Moz" => true])
                    ->finalizeMock();
        
        $this->assertTrue($user->hasRole("Foo"));
        $this->assertFalse($user->hasRole("Bar"));
        $this->assertTrue($user->hasRole("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockHasAttribute()
     */
    public function testMockHasAttribute(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockHasAttribute($this->any(), "Foo", true)->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->mockHasAttribute($this->any(), "Foo", false)->finalizeMock();
        
        $this->assertTrue($user->hasAttribute("Foo"));
        $this->assertFalse($user2->hasAttribute("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockHasAttribute_consecutive()
     */
    public function testMockHasAttribute_consecutive(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")
                            ->mockHasAttribute_consecutive($this->exactly(3), ["Foo" => true, "Bar" => false, "Moz" => true])
                            ->finalizeMock();
        
        $this->assertTrue($user->hasAttribute("Foo"));
        $this->assertFalse($user->hasAttribute("Bar"));
        $this->assertTrue($user->hasAttribute("Moz"));
    }
    
    /**
     * MUTABLE
     */
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockAddRole()
     */
    public function testMockAddRole(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->mockAddRole($this->any(), "Foo")->finalizeMock();
        
        $this->assertInstanceOf(MutableUserInterface::class, $user->addRole("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockAddRole_consecutive()
     */
    public function testMockAddRole_consecutive(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")
                            ->mockAddRole_consecutive($this->exactly(3), "Foo", "Bar", "Moz")->finalizeMock();
        
                            
        $this->assertInstanceOf(MutableUserInterface::class, $user->addRole("Foo"));
        $this->assertInstanceOf(MutableUserInterface::class, $user->addRole("Bar"));
        $this->assertInstanceOf(MutableUserInterface::class, $user->addRole("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockAddAttribute()
     */
    public function testMockAddAttribute(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->mockAddAttribute($this->any(), "Foo", "Bar")->finalizeMock();
        
        $this->assertInstanceOf(MutableUserInterface::class, $user->addAttribute("Foo", "Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockAddAttribute_consecutive()
     */
    public function testMockAddAttribute_consecutive(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")
                    ->mockAddAttribute_consecutive($this->exactly(3), ["Foo" => "Bar", "Bar" => "Foo", "Moz" => "Poz"])->finalizeMock();
        
        $this->assertInstanceOf(MutableUserInterface::class, $user->addAttribute("Foo", "Bar"));
        $this->assertInstanceOf(MutableUserInterface::class, $user->addAttribute("Bar", "Foo"));
        $this->assertInstanceOf(MutableUserInterface::class, $user->addAttribute("Moz", "Poz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockDeleteAttribute()
     */
    public function testMockDeleteAttribute(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->mockDeleteAttribute($this->any(), "Foo")->finalizeMock();
        
        $this->assertInstanceOf(MutableUserInterface::class, $user->deleteAttribute("Foo"));
        
        $this->expectException(InvalidUserAttributeException::class);
        
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->mockDeleteAttribute($this->exactly(1), "Foo", true)->finalizeMock();
        $user->deleteAttribute("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockDeleteAttribute_consecutive()
     */
    public function testMockDeleteAttribute_consecutive(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")
                                ->mockDeleteAttribute_consecutive($this->exactly(3), ["Foo" => false, "Bar" => false, "Moz" => false])
                                ->finalizeMock();
        
        $this->assertInstanceOf(MutableUserInterface::class, $user->deleteAttribute("Foo"));
        $this->assertInstanceOf(MutableUserInterface::class, $user->deleteAttribute("Bar"));
        $this->assertInstanceOf(MutableUserInterface::class, $user->deleteAttribute("Moz"));
        
        $this->expectException(InvalidUserAttributeException::class);
        
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")
                                ->mockDeleteAttribute_consecutive($this->exactly(2), ["Foo" => false, "Bar" => true, "Moz" => false])
                                ->finalizeMock();
        
        $this->assertInstanceOf(MutableUserInterface::class, $user->deleteAttribute("Foo"));
        $user->deleteAttribute("Bar");
    }
    
    /**
     * CREDENTIAL
     */
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetPassword()
     */
    public function testMockGetPassword(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetPassword($this->any(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $user->getPassword());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetCredentials()
     */
    public function testMockGetCredentials(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")
                                ->mockGetCredentials($this->any(), ["Foo" => "Bar", "Bar" => "Foo"])
                                ->finalizeMock();
        
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $user->getCredentials());
        
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")
                                ->mockGetCredentials($this->any(), null)
                                ->finalizeMock();
        
        $this->assertNull($user->getCredentials());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetCredentials_consecutive()
     */
    public function testMockGetCredentials_consecutive(): void
    {
        $credentials = [
            ["Foo" => "Bar", "Bar" => "Foo", "Moz" => "Poz"],
            ["Foo" => "Bar", "Bar" => "Foo"],
            ["Foo" => "Bar"],
            null
        ];
        
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")
                                ->mockGetCredentials_consecutive(
                                    $this->exactly(4), $credentials)
                                ->finalizeMock();
        
        $this->assertSame($credentials[0], $user->getCredentials());
        $this->assertSame($credentials[1], $user->getCredentials());
        $this->assertSame($credentials[2], $user->getCredentials());
        $this->assertNull($user->getCredentials());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetCredential()
     */
    public function testMockGetCredential(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetCredential($this->any(), "Foo", "Bar")->finalizeMock();
        
        $this->assertSame("Bar", $user->getCredential("Foo"));
        
        $this->expectException(\Exception::class);
        
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")
                                ->mockGetCredential($this->exactly(1), "Foo", new \Exception())
                                ->finalizeMock();
        
        $user->getCredential("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetCredential__consecutive()
     */
    public function testMockGetCredential_consecutive(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")
                                ->mockGetCredentical_consecutive($this->exactly(3), ["Foo" => "Bar", "Bar" => "Foo", "Moz" => "Poz"])
                                ->finalizeMock();
        
        $this->assertSame("Bar", $user->getCredential("Foo"));
        $this->assertSame("Foo", $user->getCredential("Bar"));
        $this->assertSame("Poz", $user->getCredential("Moz"));
        
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")
                                ->mockGetCredentical_consecutive(
                                    $this->exactly(2), 
                                    ["Foo" => "Bar", "Bar" => new \Exception(), "Moz" => "Poz"])
                                ->finalizeMock();
        
        $this->expectException(\Exception::class);
        $this->assertSame("Bar", $user->getCredential("Foo"));
        $user->getCredential("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockHasCredential()
     */
    public function testMockHasCredential(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockHasCredential($this->any(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($user->hasCredential("Foo"));
        
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockHasCredential($this->any(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($user->hasCredential("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockHasCredential_consecutive()
     */
    public function testMockHasCredential_consecutive(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")
                                ->mockHasCredential_consecutive($this->exactly(3), ["Foo" => true, "Bar" => false, "Moz" => true])
                                ->finalizeMock();
        
        $this->assertTrue($user->hasCredential("Foo"));
        $this->assertFalse($user->hasCredential("Bar"));
        $this->assertTrue($user->hasCredential("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockAddCredential()
     */
    public function testMockAddCredential(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockAddCredential($this->any(), "Foo", "Bar")->finalizeMock();
        
        $this->assertInstanceOf(CredentialUserInterface::class, $user->addCredential("Foo", "Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockAddCredential_consecutive()
     */
    public function testMockAddCredential_consecutive(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")
                                ->mockAddCredential_consecutive(
                                    $this->exactly(3), 
                                    ["Foo" => "Bar", "Bar" => "Foo", "Moz" => "Poz"])
                                ->finalizeMock();
        
        $this->assertInstanceOf(CredentialUserInterface::class, $user->addCredential("Foo", "Bar"));
        $this->assertInstanceOf(CredentialUserInterface::class, $user->addCredential("Bar", "Foo"));
        $this->assertInstanceOf(CredentialUserInterface::class, $user->addCredential("Moz", "Poz"));
    }
    
    /**
     * ACL
     */
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGrant()
     */
    public function testMockGrant(): void
    {
        $reflection = new \ReflectionClass(ResourceInterface::class);
        $resource = $this->getMockBuilder(ResourceInterface::class)->setMethods($this->reflection_extractMethods($reflection))->getMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->mockGrant($this->any(), $resource, ["Foo", "Bar"])->finalizeMock();
        
        $this->assertNull($user->grant($resource, ["Foo", "Bar"]));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockDeny()
     */
    public function testMockDeny(): void
    {
        $reflection = new \ReflectionClass(ResourceInterface::class);
        $resource = $this->getMockBuilder(ResourceInterface::class)->setMethods($this->reflection_extractMethods($reflection))->getMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->mockDeny($this->any(), $resource, ["Foo", "Bar"])->finalizeMock();
        
        $this->assertNull($user->grant($resource, ["Foo", "Bar"]));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetPermission()
     */
    public function testMockGetPermission(): void
    {
        $reflection = new \ReflectionClass(Mask::class);
        $mask = $this
                    ->getMockBuilder(Mask::class)
                    ->setMethods($this->reflection_extractMethods($reflection))
                    ->disableOriginalConstructor()
                    ->getMock();
        
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->mockGetPermission($this->any(), "Foo", $mask)->finalizeMock();
        $this->assertInstanceOf(Mask::class, $user->getPermission("Foo"));
        
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->mockGetPermission($this->once(), "Foo", null)->finalizeMock();
        $this->expectException(\Exception::class);
        $user->getPermission("Foo");
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::__construct()
     */
    public function testInitialize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("This type 'Foo' is not valid. Use 'Zoe\Component\Security\User\Contracts\UserInterface or Zoe\Component\Security\User\Contracts\MutableUserInterface or Zoe\Component\Security\User\Contracts\StorableUserInterface or Zoe\Component\Security\User\Contracts\AclUserInterface or Zoe\Component\Security\User\MutableAclUser or Zoe\Component\Security\User\Contracts\CredentialUserInterface'");
        
        $mock = new UserMock("Foo", "Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'getName' for mocked user 'Foo' has been already mocked");
        
        $user = UserMock::initMock(UserInterface::class, "Foo");
        $user->mockGetName($this->any());
        $user->mockGetName($this->any());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock
     */
    public function testExceptionWhenAnInvalidUserTypeIsGivenForAMethod(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Impossible to mock this method 'addRole' on this user type 'Zoe\Component\Security\User\Contracts\UserInterface'. Use a valid one : 'Zoe\Component\Security\User\Contracts\MutableUserInterface, Zoe\Component\Security\User\MutableAclUser, Zoe\Component\Security\User\Contracts\CredentialUserInterface'");
        
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockAddRole($this->any(), "Foo")->finalizeMock();
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserMock::mockGetCredential()
     */
    public function testExceptionOnInvalidTypeDuringGetCredentialMocking(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Value for credential 'Foo' MUST be an instance of Exception or a string on user 'Bar'");
        
        $user = UserMock::initMock(CredentialUserInterface::class, "Bar")
                                ->mockGetCredential($this->exactly(1), "Foo", true)
                                ->finalizeMock();
    }
    
}
