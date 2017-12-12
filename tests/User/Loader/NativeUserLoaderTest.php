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

namespace ZoeTest\Component\Security\User\Loader;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\Exception\User\UserNotFoundException;
use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\User\Loader\NativeUserLoader;

/**
 * NativeUserLoader testcase
 * 
 * @see \Zoe\Component\Security\User\Loader\NativeUserLoader
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeUserLoaderTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader::loadUser()
     */
    public function testLoadUser(): void
    {
        $users = [
            "Foo"   =>  [
                // empty
            ],
            "Bar"   =>  [
                "password"      =>  "Foo",
                "roles"         =>  ["Foo", "Bar"],
                "attributes"    =>  [
                    "Foo"           =>  "Bar",
                    "Bar"           =>  "Foo"
                ],
                "credentials"   =>  [
                    "Moz"           =>  "Poz",
                    "Poz"           =>  "Moz"
                ]
            ]
        ];
        
        $loader = new NativeUserLoader($users);
        // load a mocked user to make sure that no information about the given user are leaked into the loaded one
        $user = function(string $name): AuthenticationUserInterface {
            return UserMock::init("UserGivenToLoader", AuthenticationUserInterface::class)
                                ->mockGetName($this->once(), $name)
                                ->mockGetAttributes($this->never(), [])
                                ->mockGetRoles($this->never(), [])
                                ->mockGetCredentials($this->never(), [])
                                ->mockGetPassword($this->never(), null)
                            ->finalizeMock();
        };
        
        $fooUser = $loader->loadUser($user("Foo"));
        $barUser = $loader->loadUser($user("Bar"));
        
        $this->assertSame("Foo", $fooUser->getName());
        $this->assertNull($fooUser->getPassword());
        $this->assertEmpty($fooUser->getRoles());
        $this->assertEmpty($fooUser->getAttributes());
        $this->assertSame(["USER_PASSWORD" => null], $fooUser->getCredentials());
        
        $this->assertSame("Bar", $barUser->getName());
        $this->assertSame("Foo", $barUser->getPassword());
        $this->assertSame(["Foo" => "Foo", "Bar" => "Bar"], $barUser->getRoles());
        $this->assertSame(["Foo" => "Bar", "Bar" => "Foo"], $barUser->getAttributes());
        $this->assertSame(["Moz" => "Poz", "Poz" => "Moz", "USER_PASSWORD" => "Foo"], $barUser->getCredentials());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader::loadUser()
     */
    public function testExceptionLoadUserWhenUserNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be loaded");
        
        $loader = new NativeUserLoader([]);
        $user = UserMock::init("UserGivenToLoader", AuthenticationUserInterface::class)
                            ->mockGetName($this->exactly(2), "Foo") // called 2 times as exception to a call
                        ->finalizeMock();
        $loader->loadUser($user);
    }
    
}
