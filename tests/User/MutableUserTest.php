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

namespace Zoe\Component\Security\User\MutableUser;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\User\MutableUser;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;

/**
 * MutableUser testcase
 * 
 * @see \Zoe\Component\Security\User\MutableUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MutableUserTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\MutableUser
     */
    public function testInterface(): void
    {
        $user = new MutableUser("foo");
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(MutableUserInterface::class, $user);
    }
    
    /**
     * @see \Zoe\Component\Security\User\MutableUser::addRole()
     */
    public function testAddRole(): void
    {
        $user = new MutableUser("foo");
        
        $this->assertSame([], $user->getRoles());
        
        $this->assertInstanceOf(MutableUserInterface::class, $user->addRole("foo"));
        $this->assertTrue($user->hasRole("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\MutableUser::addAttribute()
     */
    public function testAddAttribute(): void
    {
        $user = new MutableUser("foo");
        
        $this->assertNull($user->getAttributes());
        
        $this->assertInstanceOf(MutableUserInterface::class, $user->addAttribute("foo", "bar"));
        $this->assertTrue($user->hasAttribute("foo"));
        $this->assertSame("bar", $user->getAttribute("foo"));
    }
    
}
