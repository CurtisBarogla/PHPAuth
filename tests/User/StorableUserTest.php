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
use Zoe\Component\Security\User\StorableUser;
use Zoe\Component\Security\User\StorableUserInterface;
use Zoe\Component\Security\User\User;

class StorableUserTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\StorableUser
     */
    public function testInterface(): void
    {
        $user = new StorableUser("foo", [], null);
        
        $this->assertInstanceOf(StorableUserInterface::class, $user);
    }
    
    /**
     * @see \Zoe\Component\Security\User\StorableUser::createFromUser()
     */
    public function testCreateFromUser(): void
    {
        // not root
        $user = new User("foo", "bar", []);
        $user->addRole("foo")->addAttribute("foo", "bar");
        
        $storedUser = StorableUser::createFromUser($user);
        $this->assertInstanceOf(StorableUserInterface::class, $storedUser);
        $this->assertSame("foo", $storedUser->getName());
        $this->assertSame(["foo" => "foo"], $storedUser->getRoles());
        $this->assertSame(["foo" => "bar"], $storedUser->getAttributes());
        $this->assertFalse($storedUser->isRoot());
        
        // root
        $user = new User("foo", "bar", [], true);
        $storedUser = StorableUser::createFromUser($user);
        
        $this->assertTrue($storedUser->isRoot());
    }
    
    /**
     * @see \Zoe\Component\Security\User\StorableUser::__toString()
     */
    public function test__toString(): void
    {
        $this->expectOutputString("foo");
        
        $user = new StorableUser("foo", [], null);
        
        echo $user;
    }
    
}
