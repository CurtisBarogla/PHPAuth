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
use ZoeTest\Component\Security\Fixtures\User\UserFixture;
use Zoe\Component\Security\Exception\InvalidUserAttributeException;
use Zoe\Component\Security\User\Contracts\UserInterface;

/**
 * User testcase
 * 
 * @see \Zoe\Component\Security\User\User
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserTest extends SecurityTestCase
{

    /**
     * @see \Zoe\Component\Security\User\User::__construct()
     */
    public function testInitialization(): void
    {
        $user = new UserFixture("foo");
        $reflection = new \ReflectionClass($user);
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertSame([], $this->reflection_getPropertyValue($user, $reflection, "roles"));
        $this->assertSame(null, $this->reflection_getPropertyValue($user, $reflection, "attributes"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::__construct()
     */
    public function testInitializationWithSettedValues(): void
    {
        $user = new UserFixture("foo", false, ["foo", "bar"], ["foo" => "bar", "bar" => "foo"]);
        $reflection = new \ReflectionClass($user);
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $this->reflection_getPropertyValue($user, $reflection, "roles"));
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $this->reflection_getPropertyValue($user, $reflection, "attributes"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getName()
     */
    public function testGetName(): void
    {
        $user = new UserFixture("foo");
        
        $this->assertSame("foo", $user->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::isRoot()
     */
    public function testIsRoot(): void
    {
        $user = new UserFixture("foo");
        
        $this->assertFalse($user->isRoot());
        
        $user = new UserFixture("foo", true);
        
        $this->assertTrue($user->isRoot());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getRoles()
     */
    public function testGetRoles(): void
    {
        $user = new UserFixture("foo");
        
        $this->assertSame([], $user->getRoles());
        
        $user = new UserFixture("foo", false, ["foo", "bar"]);
        
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $user->getRoles());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttributes()
     */
    public function testGetAttributes(): void
    {
        $user = new UserFixture("foo");
        
        $this->assertNull($user->getAttributes());
        
        $user = new UserFixture("foo", false, [], ["foo" => "bar", "bar" => "foo"]);
        
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $user->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttribute()
     */
    public function testGetAttribute(): void
    {
        $user = new UserFixture("foo", false, [], ["foo" => "bar", "bar" => "foo"]);
        
        $this->assertSame("bar", $user->getAttribute("foo"));
        $this->assertSame("foo", $user->getAttribute("bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::hasRole()
     */
    public function testHasRole(): void
    {
        $user = new UserFixture("foo");
        
        $this->assertFalse($user->hasRole("foo"));
        
        $user = new UserFixture("foo", false, ["foo"]);
        
        $this->assertTrue($user->hasRole("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::hasAttribute()
     */
    public function testHasAttribute(): void
    {
        $user = new UserFixture("foo");
        
        $this->assertFalse($user->hasAttribute("foo"));
        
        $user = new UserFixture("foo", false, [], ["foo" => "bar"]);
        
        $this->assertTrue($user->hasAttribute("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\User::__toString()
     */
    public function test__toString(): void
    {
        $this->expectOutputString("foo");
        
        $user = new UserFixture("foo");
        
        echo $user;
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\User::getAttribute()
     */
    public function testExceptionWhenGettingAnInvalidAttribute(): void
    {
        $this->expectException(InvalidUserAttributeException::class);
        $this->expectExceptionMessage("This attribute 'foo' for the user 'bar' is not setted");
        
        $user = new UserFixture("bar");
        
        $user->getAttribute("foo");
    }
    
}
