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

namespace ZoeTest\Component\Security\MockGeneration\Tests\User;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Exception\User\InvalidUserAttributeException;
use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\Exception\User\InvalidUserRoleException;
use Zoe\Component\Security\Exception\User\InvalidUserCredentialException;
use Zoe\Component\Security\User\AuthenticatedUserInterface;

/**
 * UserMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\User;
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserMockTest extends TestCase
{
    
    // Common
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetName()
     */
    public function testMockGetName(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)->mockGetName($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $user->getName());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockIsRoot()
     */
    public function testMockIsRoot(): void
    {
        $user = UserMock::init("Foo", AuthenticatedUserInterface::class)->mockIsRoot($this->once(), true)->finalizeMock();
        
        $this->assertTrue($user->isRoot());
        
        $user = UserMock::init("Foo", AuthenticatedUserInterface::class)->mockIsRoot($this->once(), false)->finalizeMock();
        
        $this->assertFalse($user->isRoot());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockIsRoot_consecutive()
     */
    public function testMockIsRoot_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticatedUserInterface::class)->mockIsRoot_consecutive($this->exactly(2), true, false)->finalizeMock();
        
        $this->assertTrue($user->isRoot());
        $this->assertFalse($user->isRoot());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockAddAttribute()
     */
    public function testMockAddAttribute(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)->mockAddAttribute($this->once(), "Foo", "Bar")->finalizeMock();
        
        $this->assertNull($user->addAttribute("Foo", "Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockAddAttribute_consecutive()
     */
    public function testMockAddAttribute_consecutive(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockAddAttribute_consecutive(
                                $this->exactly(3), 
                                    ["Foo", "Bar"],  // first call
                                    ["Bar", "Foo"],  // second call
                                    ["Moz", "Poz"])  // third call
                        ->finalizeMock();
        
        $this->assertNull($user->addAttribute("Foo", "Bar"));
        $this->assertNull($user->addAttribute("Bar", "Foo"));
        $this->assertNull($user->addAttribute("Moz", "Poz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetAttributes()
     */
    public function testMockGetAttributes(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)->mockGetAttributes($this->once(), ["Foo" => "Bar", "Bar" => "Foo"])->finalizeMock();
        
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $user->getAttributes());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetAttributes_consecutive()
     */
    public function testMockGetAttributes_consecutive(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockGetAttributes_consecutive(
                                $this->exactly(2), 
                                ["Foo" => "Bar", "Bar" => "Foo"],
                                ["Moz" => "Poz", "Poz" => "Moz"])
                        ->finalizeMock();
        
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $user->getAttributes());
        $this->assertSame(["Moz" => "Poz", "Poz" => "Moz"], $user->getAttributes());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetAttribute()
     */
    public function testMockGetAttribute(): void
    {
        // valid
        $user = UserMock::init("Foo", UserInterface::class)->mockGetAttribute($this->once(), "Foo", "Bar")->finalizeMock();
        
        $this->assertSame("Bar", $user->getAttribute("Foo"));
        
        // exception
        $this->expectException(InvalidUserAttributeException::class);
        $user = UserMock::init("Foo", UserInterface::class)->mockGetAttribute($this->once(), "Foo", new \Exception())->finalizeMock();
        
        $user->getAttribute("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetAttribute_consecutive()
     */
    public function testMockGetAttribute_consecutive(): void
    {
        // valid
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockGetAttribute_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                "Bar", "Foo")
                        ->finalizeMock();
        
        $this->assertSame("Bar", $user->getAttribute("Foo"));
        $this->assertSame("Foo", $user->getAttribute("Bar"));
        
        // exception
        $this->expectException(InvalidUserAttributeException::class);
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockGetAttribute_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                "Bar", new \Exception())
                        ->finalizeMock();
        
        $this->assertSame("Bar", $user->getAttribute("Foo"));
        $user->getAttribute("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockHasAttribute()
     */
    public function testMockHasAttribute(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)->mockHasAttribute($this->once(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($user->hasAttribute("Foo"));
        
        $user = UserMock::init("Foo", UserInterface::class)->mockHasAttribute($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($user->hasAttribute("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockHasAttribute_consecutive()
     */
    public function testMockHasAttribute_consecutive(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockHasAttribute_consecutive(
                                $this->exactly(3), 
                                [["Foo"], ["Bar"], ["Moz"]], 
                                true, false, true)
                        ->finalizeMock();
        
        $this->assertTrue($user->hasAttribute("Foo"));
        $this->assertFalse($user->hasAttribute("Bar"));
        $this->assertTrue($user->hasAttribute("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockDeleteAttribute()
     */
    public function testMockDeleteAttribute(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)->mockDeleteAttribute($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertNull($user->deleteAttribute("Foo"));
        
        $this->expectException(InvalidUserAttributeException::class);
        $user = UserMock::init("Foo", UserInterface::class)->mockDeleteAttribute($this->once(), "Foo", true)->finalizeMock();
        $user->deleteAttribute("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockDeleteAttribute_consecutive()
     */
    public function testMockDeleteAttribute_consecutive(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockDeleteAttribute_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                false, false)
                        ->finalizeMock();
        
        $this->assertNull($user->deleteAttribute("Foo"));
        $this->assertNull($user->deleteAttribute("Bar"));
        
        $this->expectException(InvalidUserAttributeException::class);
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockDeleteAttribute_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                false, true)
                        ->finalizeMock();
        
        $this->assertNull($user->deleteAttribute("Foo"));
        $user->deleteAttribute("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetRoles()
     */
    public function testMockGetRoles(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)->mockGetRoles($this->once(), ["Foo", "Bar", "Moz"])->finalizeMock();
        
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar", "Moz" => "Moz"], $user->getRoles());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetRoles_consecutive()
     */
    public function testMockGetRoles_consecutive(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockGetRoles_consecutive($this->exactly(2), ["Foo", "Bar"], ["Foo"])
                        ->finalizeMock();
        
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $user->getRoles());
        $this->assertSame(["Foo" => "Foo"], $user->getRoles());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockHasRole()
     */
    public function testMockHasRole(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)->mockHasRole($this->once(), "Foo", true)->finalizeMock(); 
        
        $this->assertTrue($user->hasRole("Foo"));
        
        $user = UserMock::init("Foo", UserInterface::class)->mockHasRole($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($user->hasRole("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockHasRole_consecutive()
     */
    public function testMockHasRole_consecutive(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)
                            ->mockHasRole_consecutive(
                                $this->exactly(3), 
                                [["Foo"], ["Bar"], ["Moz"]], 
                                true, false, true)
                        ->finalizeMock();
        
        $this->assertTrue($user->hasRole("Foo"));
        $this->assertFalse($user->hasRole("Bar"));
        $this->assertTrue($user->hasRole("Moz"));
    }
    
    // AuthenticationUser
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockChangeName()
     */
    public function testMockChangeName(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockChangeName($this->once(), "Foo")->finalizeMock();
        
        $this->assertNull($user->changeName("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetPassword()
     */
    public function testMockGetPassword(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockGetPassword($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $user->getPassword());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetPassword_consecutive()
     */
    public function testMockGetPassword_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockGetPassword_consecutive($this->exactly(2), "Foo", null)->finalizeMock();
        
        $this->assertSame("Foo", $user->getPassword());
        $this->assertNull($user->getPassword());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockAddRole()
     */
    public function testMockAddRole(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockAddRole($this->once(), "Foo")->finalizeMock();
        
        $this->assertNull($user->addRole("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockAddRole_consecutive()
     */
    public function testMockAddRole_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockAddRole_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]])
                        ->finalizeMock();
        
        $this->assertNull($user->addRole("Foo"));
        $this->assertNull($user->addRole("Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockDeleteRole()
     */
    public function testMockDeleteRole(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockDeleteRole($this->once(), "Foo")->finalizeMock();
        
        $this->assertNull($user->deleteRole("Foo"));
        
        $this->expectException(InvalidUserRoleException::class);
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockDeleteRole($this->once(), "Foo", true)->finalizeMock();
        $user->deleteRole("Foo");
    }
        
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockDeleteRole_consecutive()
     */
    public function testMockDelete_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockDeleteRole_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                false, false)
                        ->finalizeMock();
        
        $this->assertNull($user->deleteRole("Foo"));
        $this->assertNull($user->deleteRole("Bar"));
        
        $this->expectException(InvalidUserRoleException::class);
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockDeleteRole_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                false, true)
                        ->finalizeMock();
        
        $this->assertNull($user->deleteRole("Foo"));
        $user->deleteRole("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockAddCredential()
     */
    public function testMockAddCredential(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockAddCredential($this->once(), "Foo", "Bar")->finalizeMock();
        
        $this->assertNull($user->addCredential("Foo", "Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockAddCredential_consecutive()
     */
    public function testMockAddCredential_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockAddCredential_consecutive(
                                $this->exactly(2), 
                                [["Foo", "Bar"], ["Bar", "Foo"]])
                        ->finalizeMock();
        
        $this->assertNull($user->addCredential("Foo", "Bar"));
        $this->assertNull($user->addCredential("Bar", "Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetCredentials()
     */
    public function testMockGetCredentials(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockGetCredentials($this->once(), ["Foo" => "Bar", "Bar" => "Foo"])->finalizeMock();
        
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $user->getCredentials());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetCredentials_consecutive()
     */
    public function testMockGetCredentials_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockGetCredentials_consecutive(
                                $this->exactly(2), 
                                ["Foo" => "Bar", "Bar" => "Foo"],
                                ["Foo" => "Bar"])
                        ->finalizeMock();
        
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $user->getCredentials());
        $this->assertSame(["Foo" => "Bar"], $user->getCredentials());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetCredential()
     */
    public function testMockGetCredential(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockGetCredential($this->once(), "Foo", "Bar")->finalizeMock();
        
        $this->assertSame("Bar", $user->getCredential("Foo"));
        
        $this->expectException(InvalidUserCredentialException::class);
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockGetCredential($this->once(), "Foo", new \Exception())->finalizeMock();
        
        $user->getCredential("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetCredential_consecutive()
     */
    public function testMockGetCredential_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockGetCredential_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                "Bar", "Foo")
                        ->finalizeMock();
        
        $this->assertSame("Bar", $user->getCredential("Foo"));
        $this->assertSame("Foo", $user->getCredential("Bar"));
        
        $this->expectException(InvalidUserCredentialException::class);
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockGetCredential_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                "Bar", new \Exception())
                        ->finalizeMock();
        
        $this->assertSame("Bar", $user->getCredential("Foo"));
        $user->getCredential("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockHasCredential()
     */
    public function testMockHasCredential(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockHasCredential($this->once(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($user->hasCredential("Foo"));
        
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockHasCredential($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($user->hasCredential("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockHasCredential_consecutive()
     */
    public function testMockHasCredential_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockHasCredential_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                true, false)
                        ->finalizeMock();
        
        $this->assertTrue($user->hasCredential("Foo"));
        $this->assertFalse($user->hasCredential("Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockDeleteCredential()
     */
    public function testMockDeleteCredential(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockDeleteCredential($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertNull($user->deleteCredential("Foo"));
        
        $this->expectException(InvalidUserCredentialException::class);
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockDeleteCredential($this->once(), "Foo", true)->finalizeMock();
        
        $user->deleteCredential("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockDeleteCredential_consecutive()
     */
    public function testMockDeleteCredential_consecutive(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)
                            ->mockDeleteCredential_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                false, false)
                        ->finalizeMock();
        
        $this->assertNull($user->deleteCredential("Foo"));
        $this->assertNull($user->deleteCredential("Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockDeleteCredentials()
     */
    public function testMockDeleteCredentials(): void
    {
        $user = UserMock::init("Foo", AuthenticationUserInterface::class)->mockDeleteCredentials($this->once())->finalizeMock();
        
        $this->assertNull($user->deleteCredentials());
    }
    
    // AuthenticatedUser
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockAuthenticatedAt()
     */
    public function testMockAuthenticatedAt(): void
    {
        $user = UserMock::init("Foo", AuthenticatedUserInterface::class)->mockAuthenticatedAt($this->once(), new \DateTime())->finalizeMock();
        
        $now = (new \DateTime())->format("d/m/Y H:i:s");
        
        $this->assertSame($now, $user->authenticatedAt()->format("d/m/Y H:i:s"));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::init()
     */
    public function testExceptionInitWhenUserInvalid(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Given user type 'Bar' is invalid. Use : 'Zoe\Component\Security\User\UserInterface | Zoe\Component\Security\User\AuthenticationUserInterface | Zoe\Component\Security\User\AuthenticatedUserInterface'");
        
        $user = UserMock::init("Foo", "Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock
     */
    public function testExceptionWhenUserTypeInvalidForMethodNeededAnAuthenticationUser(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This user type 'Zoe\Component\Security\User\UserInterface' for this method 'getPassword' is not valid. Use : 'Zoe\Component\Security\User\AuthenticationUserInterface'");
        
        $user = UserMock::init("Foo", UserInterface::class)->mockGetPassword($this->once(), "Foo")->finalizeMock();
    }
    
}
