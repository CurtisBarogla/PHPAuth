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
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
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
        
        $user = UserMock::initMock(UserInterface::class, "foo")->mockGetName($this->atLeastOnce())->finalizeMock();
        
        $loader = new NativeUserLoader($users);
        
        $user = $loader->loadUser($user);
        
        $this->assertInstanceOf(MutableUserInterface::class, $user);
        $this->assertTrue($user->isRoot());
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $user->getRoles());
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $user->getAttributes());
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
        $user = UserMock::initMock(UserInterface::class, "foo")->mockGetName($this->atLeastOnce())->finalizeMock();
        
        $loader = new NativeUserLoader($users);
        $user = $loader->loadUser($user);
        
        $this->assertInstanceOf(CredentialUserInterface::class, $user);
        $this->assertSame("foo", $user->getPassword());
        $this->assertTrue($user->isRoot()).
        $this->assertSame(["foo" => "bar", "bar" => "foo", "password" => "foo"], $user->getCredentials());
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $user->getRoles());
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $user->getAttributes());
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
        $this->expectExceptionMessage("This user 'Foo' does not exist");
        
        $loader = new NativeUserLoader([]);
        $loader->loadUser(UserMock::initMock(UserInterface::class, "Foo")->mockGetName($this->exactly(2))->finalizeMock());
    }
    
}
