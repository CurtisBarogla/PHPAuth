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

namespace ZoeTest\Component\Security\Loader;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\User\Loader\NativeUserLoader;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\User;

/**
 * NativeUserLoader testcase
 * 
 * @see \Zoe\Component\Security\Loader\NativeUserLoader
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeUserLoaderTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader
     */
    public function testInterface(): void
    {
        $loader = new NativeUserLoader([]);
        $this->assertInstanceOf(UserLoaderInterface::class, $loader);
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader::loadUser()
     */
    public function testErrorWhenNotFoundedUserIsGiven(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'foo' does not exist");
        
        $users = [];
        $loader = new NativeUserLoader($users);
        $user = new User("foo", "bar");
        $loader->loadUser($user);
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader::loadUser()
     */
    public function testLoadUser(): void
    {
        $expectedUser1 = new User("foo", "bar", ["ROLE_FOO", "ROLE_BAR"], true, ["foo" => "bar", "bar" => "foo"]);
        $expectedUser2 = new User("bar", "foo", ["ROLE_FOO"]);
        $users = [
            "foo"   =>  [
                "password"      =>  "bar",
                "roles"         =>  ["ROLE_FOO", "ROLE_BAR"],
                "attributes"    =>  ["foo" => "bar", "bar" => "foo"],
                "root"          =>  true
            ],
            "bar"   =>  [
                "password"      =>  "foo",
                "roles"         =>  ["ROLE_FOO"],
            ]
        ];
        
        $loader = new NativeUserLoader($users);
        $this->assertEquals($expectedUser1, $loader->loadUser(new User("foo", "bar")));
        $this->assertEquals($expectedUser2, $loader->loadUser(new User("bar", "bar")));
    }
    
}
