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
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\StorableAclUser;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;
use Zoe\Component\Security\Acl\Mask\MaskCollection;

/**
 * StorableAclUser testcase
 * 
 * @see \Zoe\Component\Security\User\StorableAclUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class StorableAclUserTest extends AclUserTest
{
    
    /**
     * @see \Zoe\Component\Security\User\StorableAclUser::__construct()
     */
    public function testInitialization(): void
    {
        $user = new StorableAclUser("foo", true, ["foo", "bar"], ["foo" => "bar", "bar" => "foo"]);
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(StorableUserInterface::class, $user);
        $this->assertInstanceOf(\JsonSerializable::class, $user);
        $this->assertTrue($user->isRoot());
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $user->getRoles());
        $this->assertSame("bar", $user->getAttribute("foo"));
        $this->assertSame("foo", $user->getAttribute("bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\StorableAclUser::setPermissions()
     */
    public function testSetPermission(): void
    {
        $user = new StorableAclUser("foo");
        
        $permissions = new MaskCollection("foo");
        $permissions->add($this->getMockedMask("foo", 0x0001));
        
        $this->assertNull($user->setPermissions($permissions));
        $this->assertSame(0x0001, $user->getPermission("foo")->getValue());
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\AclUserTest::getUser()
     */
    protected function getUser(): AclUserInterface
    {
        return new StorableAclUser("foo");
    }
    
}
