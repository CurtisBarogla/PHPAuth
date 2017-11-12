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

namespace ZoeTest\Component\Security\Authentication;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Authentication\Authentication;
use Zoe\Component\Security\Authentication\AuthenticationInterface;
use Zoe\Component\Security\Exception\AuthenticationFailedException;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\Storage\UserStorageInteface;
use Zoe\Component\Security\User\StorableUser;
use Zoe\Component\Security\User\StorableUserInterface;

/**
 * Authentication testcase
 * 
 * @see \Zoe\Component\Security\Authentication\Authentication
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication
     */
    public function testInterface(): void
    {
        $mock = $this->getMockBuilder(Authentication::class)->disableOriginalConstructor()->setMethods(["authenticate"])->getMock();
        
        $this->assertInstanceOf(AuthenticationInterface::class, $mock);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::setStorage()
     */
    public function testSetStorage(): void
    {
        $store = $this->getMockedStorage();
        $loader = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"));
        $strategy = $this->getMockedAuthenticateStrategy();
        
        $authentication = new Authentication($loader, $strategy);
        $this->assertNull($authentication->setStorage($store));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testAuthenticate(): void
    {
        $store = $this->getMockedStorage();
        $strategy = $this->getMockedAuthenticateStrategy($this->getMockedUser("foo", "bar"), $this->getMockedUser("foo", "bar"), true);
        $loader = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"));
        $store
            ->method("addUser")
            ->with(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUser::createFromUser($this->getMockedUser("foo", "bar")))
            ->will($this->returnValue(null));
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->setStorage($store);
        
        $this->assertNull($authentication->authenticate($this->getMockedUser("foo", "bar")));
    }
    
    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testExceptionWhenLoaderDoesNotFoundAUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        
        $store = $this->getMockedStorage();
        $loader = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"), true);
        $strategy = $this->getMockedAuthenticateStrategy($this->getMockedUser("foo", "bar"), $this->getMockedUser("foo", "bar"), true);
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->authenticate($this->getMockedUser("foo", "bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testExceptionWhenProcessFails(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'foo' cannot be authenticated");

        $store = $this->getMockedStorage();
        $loader = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"));
        $strategy = $this->getMockedAuthenticateStrategy($this->getMockedUser("foo", "bar"), $this->getMockedUser("foo", "bar"), false);
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->authenticate($this->getMockedUser("foo", "bar"));
    }
    
    /**
     * Get a mocked UserStorage
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked UserStorage
     */
    private function getMockedStorage(): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(UserStorageInteface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder(UserStorageInteface::class)->setMethods($methods)->getMock();
        
        return $mock;
    }
    
}
