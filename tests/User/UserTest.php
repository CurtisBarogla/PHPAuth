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

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\Fixtures\User\UserFixture;
use Zoe\Component\Security\Exception\User\InvalidUserAttributeException;

/**
 * User testcase
 * 
 * @see \Zoe\Component\Security\User\User
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\User::getName()
     */
    public function testGetName(): void
    {
        $user = new UserFixture("Foo");
        
        $this->assertSame("Foo", $user->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::addAttribute()
     */
    public function testAddAttribute(): void
    {
        $user = new UserFixture("Foo");
        
        $this->assertNull($user->addAttribute("Foo", "Bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttributes()
     */
    public function testGetAttributes(): void
    {
        $user = new UserFixture("Foo", ["Foo" => "Bar"]);
        $user->addAttribute("Bar", "Foo");
        
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $user->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttribute()
     */
    public function testGetAttribute(): void
    {
        $user = new UserFixture("Foo", ["Foo" => "Bar"]);
        
        $this->assertSame("Bar", $user->getAttribute("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::hasAttribute()
     */
    public function testHasAttribute(): void
    {
        $user = new UserFixture("Foo", ["Foo" => "Bar"]);
        
        $this->assertTrue($user->hasAttribute("Foo"));
        $this->assertFalse($user->hasAttribute("Bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getRoles()
     */
    public function testGetRoles(): void
    {
        $user = new UserFixture("Foo", [], ["Foo", "Bar"]);
        
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $user->getRoles());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::hasRole()
     */
    public function testHasRole(): void
    {
        $user = new UserFixture("Foo", [], ["Foo"]);
        
        $this->assertTrue($user->hasRole("Foo"));
        $this->assertFalse($user->hasRole("Bar"));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttribute()
     */
    public function testExceptionGetAttributeOnInvalidAttribute(): void
    {
        $this->expectException(InvalidUserAttributeException::class);
        $this->expectExceptionMessage("This attribute 'Bar' for user 'Foo' is invalid");
        
        $user = new UserFixture("Foo");
        
        $user->getAttribute("Bar");
    }
    
}
