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

use Zoe\Component\Security\User\AclUserTest;
use Zoe\Component\Security\User\MutableAclUser;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;

/**
 * MutableAclUser testcase
 * 
 * @see \Zoe\Component\Security\User\MutableAclUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MutableAclUserTest extends AclUserTest
{
    
    /**
     * @see \Zoe\Component\Security\User\MutableAclUser::__construct()
     */
    public function testInitialization(): void
    {
        $user = new MutableAclUser("foo", true, ["foo", "bar"], ["foo" => "bar", "bar" => "foo"]);
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(MutableUserInterface::class, $user);
        $this->assertTrue($user->isRoot());
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $user->getRoles());
        $this->assertSame("bar", $user->getAttribute("foo"));
        $this->assertSame("foo", $user->getAttribute("bar"));
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AclUserTest::getUser()
     */
    protected function getUser(): AclUserInterface
    {
        return new MutableAclUser("foo");
    }

}
