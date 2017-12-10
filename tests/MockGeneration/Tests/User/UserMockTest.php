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
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserMock::mockGetName()
     */
    public function testMockGetName(): void
    {
        $user = UserMock::init("Foo", UserInterface::class)->mockGetName($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $user->getName());
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
    
}
