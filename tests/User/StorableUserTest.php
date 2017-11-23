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
use Zoe\Component\Security\User\StorableUser;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;

/**
 * StorableUser testcase
 * 
 * @see \Zoe\Component\Security\User\StorableUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class StorableUserTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\StorableUser
     */
    public function testInitialization(): void
    {
        $user = new StorableUser("foo");
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(StorableUserInterface::class, $user);
        $this->assertInstanceOf(\JsonSerializable::class, $user);
        $this->assertTrue($user instanceof \JsonSerializable);
        $this->assertTrue($user->storable());
    }
    
    /**
     * @see \Zoe\Component\Security\User\StorableUser::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $expected = \json_encode([
            "name"          =>  "foo",
            "root"          =>  false,
            "attributes"    =>  ["foo" => "bar"],
            "roles"         =>  ["foo" => "foo"]
        ]);
        $user = new StorableUser("foo", false, ["foo"], ["foo" => "bar"]);
        
        $this->assertSame($expected, \json_encode($user));
    }
    
}
