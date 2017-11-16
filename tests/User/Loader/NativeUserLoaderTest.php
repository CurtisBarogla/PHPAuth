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

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\CredentialUser;
use Zoe\Component\Security\User\MutableUser;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Loader\NativeUserLoader;

/**
 * NativeUserLoader testcase
 * 
 * @see \Zoe\Component\Security\Loader\NativeUserLoader
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeUserLoaderTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader::loadUser()
     */
    public function testLoadBasicUser(): void
    {
        $users = [
            "foo"   =>  [
                "root"          =>  true,
                "roles"         =>  ["foo", "bar"],
                "attributes"    =>  ["foo" => "bar", "bar" => "foo"]
            ]
        ];
        
        $loader = new NativeUserLoader($users);
        
        $expectedUser = (new MutableUser("foo", true))
                            ->addRole("foo")
                            ->addRole("bar")
                            ->addAttribute("foo", "bar")
                            ->addAttribute("bar", "foo");
        
        $this->assertEquals($expectedUser, $loader->loadUser($this->getMockedUser(UserInterface::class, "foo")));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader::loadUser()
     */
    public function testLoadUserWithCredentials(): void
    {
        $users =  [
            "foo"   =>  [
                "password"      =>  "foo",
                "root"          =>  true,
                "credentials"   =>  ["foo" => "bar", "bar" => "foo"],
                "roles"         =>  ["foo", "bar"],
                "attributes"    =>  ["foo" => "bar", "bar" => "foo"]
            ]
        ];
        
        $loader = new NativeUserLoader($users);
        
        $expectedUser = (new CredentialUser("foo", "foo", true))
                            ->addAttribute("foo", "bar")
                            ->addAttribute("bar", "foo")
                            ->addRole("foo")
                            ->addRole("bar")
                            ->addCredential("foo", "bar")
                            ->addCredential("bar", "foo");
        
        $this->assertEquals($expectedUser, $loader->loadUser($this->getMockedUser(UserInterface::class, "foo")));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader::identify()
     */
    public function testIdentify(): void
    {
        $loader = new NativeUserLoader([]);
        
        $this->assertSame("NativeUserLoader", $loader->identify());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\Loader\NativeUserLoader::loadUser()
     */
    public function testExceptionWhenUserHasBeenNotFound(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'foo' does not exist");
        
        $loader = new NativeUserLoader([]);
        $loader->loadUser($this->getMockedUser(UserInterface::class, "foo"));
    }
    
}
