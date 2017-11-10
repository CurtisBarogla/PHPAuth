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
use Zoe\Component\Security\Exception\InvalidUserAttributeException;
use Zoe\Component\Security\User\User;
use Zoe\Component\Security\User\UserInterface;

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
     * @see \Zoe\Component\Security\User\User
     */
    public function testInterface(): void
    {
        $user = new User("foo", "bar");
        
        $this->assertInstanceOf(UserInterface::class, $user);
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getName()
     */
    public function testGetName(): void
    {
        $user = new User("foo", "bar");
        
        $this->assertSame("foo", $user->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getPassword()
     */
    public function testGetPassword(): void
    {
        $user = new User("foo", "bar");
        
        $this->assertSame("bar", $user->getPassword());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::addRole()
     */
    public function testAddRole(): void
    {
        $user = new User("foo", "bar");
        
        $this->assertInstanceOf(UserInterface::class, $user->addRole("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::addRole()
     */
    public function testGetRoles(): void
    {
        $user = new User("foo", "bar");
        
        $user->addRole("foo")->addRole("bar");
        $expected = ["foo" => "foo", "bar" => "bar"];
        
        $this->assertSame($expected, $user->getRoles());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::hasRole()
     */
    public function testHasRole(): void
    {
        $user = new User("foo", "bar");
        
        $user->addRole("foo");
        $this->assertTrue($user->hasRole("foo"));
        $this->assertFalse($user->hasRole("bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::isRoot()
     */
    public function testIsRoot(): void
    {
        $user = new User("foo", "bar");
        
        $this->assertFalse($user->isRoot());
        
        $user = new User("foo", "bar", [], true);
        
        $this->assertTrue($user->isRoot());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::addAttribute()
     */
    public function testAddAttribute(): void
    {
        $user = new User("foo", "bar");
        
        $this->assertInstanceOf(UserInterface::class, $user->addAttribute("foo", "bar")->addAttribute("bar", "foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttributes()
     */
    public function testGetAttributes(): void
    {
        $user = new User("foo", "bar");
        
        $expected = ["foo" => "bar", "bar" => "foo"];
        $user->addAttribute("foo", "bar")->addAttribute("bar", "foo");
        
        $this->assertSame($expected, $user->getAttributes());
        
        $user = new User("foo", "bar");
        
        $this->assertNull($user->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttribute()
     */
    public function testGetAttribute(): void
    {
        $user = new User("foo", "bar");
        
        $user->addAttribute("foo", "bar");
        
        $this->assertSame("bar", $user->getAttribute("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::hasAttribute()
     */
    public function testHasAttribute(): void
    {
        $user = new User("foo", "bar");
        
        $this->assertFalse($user->hasAttribute("foo"));
        
        $user->addAttribute("foo", "bar");
        
        $this->assertTrue($user->hasAttribute("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::__toString()
     */
    public function test__toString(): void
    {
        $this->expectOutputString("foo");
        
        $user = new User("foo", "bar");
        
        echo $user;
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttribute()
     */
    public function testExceptionWhenTryingToGetAnInvalidAttribute(): void
    {
        $this->expectException(InvalidUserAttributeException::class);
        $this->expectExceptionMessage("This attribute 'foo' for user 'user' is not setted");
        
        $user = new User("user", "bar");
        
        $user->getAttribute("foo");
    }
    
}
