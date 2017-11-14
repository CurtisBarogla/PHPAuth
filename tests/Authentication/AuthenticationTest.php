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
use ZoeTest\Component\Security\Fixtures\Authentication\AuthenticationStrategyFixture;
use ZoeTest\Component\Security\Fixtures\Authentication\UserLoaderFixture;
use Zoe\Component\Security\Authentication\Authentication;
use Zoe\Component\Security\Authentication\AuthenticationInterface;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\Exception\AuthenticationFailedException;
use Zoe\Component\Security\Exception\LogicException;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\Storage\UserStorageInteface;
use Zoe\Component\Security\User\StorableUserFactory;
use Zoe\Component\Security\User\StorableUserInterface;
use Zoe\Component\Security\User\UserInterface;

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
     * @see \Zoe\Component\Security\Authentication\Authentication::switch()
     */
    public function testSwitch(): void
    {
        $newStrategy = new AuthenticationStrategyFixture();
        $newUserLoader = new UserLoaderFixture();
        $currentStrategy = $this->getMockedAuthenticateStrategy();
        $currentLoader = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"));
        
        $authentication = new Authentication($currentLoader, $currentStrategy);
        $reflection = new \ReflectionClass($authentication);

        $getProperty = function(AuthenticationInterface $authentication, string $property) use ($reflection) {
            return $this->reflection_getPropertyValue($authentication, $reflection, $property);
        };
        
        $this->assertInstanceOf(\PHPUnit_Framework_MockObject_MockObject::class, $getProperty($authentication, "loader"));
        $this->assertInstanceOf(\PHPUnit_Framework_MockObject_MockObject::class, $getProperty($authentication, "strategy"));
        
        $authentication = $authentication->switch($newUserLoader, null);
        $this->assertInstanceOf(UserLoaderFixture::class, $getProperty($authentication, "loader"));
        $this->assertInstanceOf(\PHPUnit_Framework_MockObject_MockObject::class, $getProperty($authentication, "strategy"));
        
        $authentication = $authentication->switch(null, $newStrategy);
        $this->assertInstanceOf(UserLoaderFixture::class, $getProperty($authentication, "loader"));
        $this->assertInstanceOf(AuthenticationStrategyFixture::class, $getProperty($authentication, "strategy"));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testAuthenticate(): void
    {
        $user = $this->getMockedUser("foo", "bar");
        $loader = $this->getMockedUserLoader("foo", $user);
        $strategy = $this->getMockedAuthenticateStrategy($user, $user, AuthenticationStrategyInterface::SUCCESS);
        $store = $this->getMockedStorage(["refreshUser", "addUser"], $user, [true, false]);
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->setStorage($store);
        
        $this->assertNull($authentication->authenticate($user));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::switch()
     */
    public function testExceptionSwitchWhenBothUserLoaderAndAuthenticationStrategyAreNull(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("UserLoader and AuthenticationStrategy cannot be both null during switching process");
        
        $authentication = new Authentication(
            $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar")), 
            $this->getMockedAuthenticateStrategy());
        
        $authentication->switch(null, null);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testExceptionWhenLoaderDoesNotFoundAUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        
        $store = $this->getMockedStorage();
        $loader = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"), true);
        $strategy = $this->getMockedAuthenticateStrategy(
                                    $this->getMockedUser("foo", "bar"), 
                                    $this->getMockedUser("foo", "bar"), AuthenticationStrategyInterface::SUCCESS);
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->authenticate($this->getMockedUser("foo", "bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testExceptionWhenStrategyProcessFailed(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'foo' cannot be authenticated");
        
        $user = $this->getMockedUser("foo", "bar");
        
        $store = $this->getMockedStorage();
        $loader = $this->getMockedUserLoader("foo", $user);
        $strategy = $this->getMockedAuthenticateStrategy($user, $user, AuthenticationStrategyInterface::FAIL);
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testExceptionWhenStrategyProcessSkipped(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'foo' cannot be authenticated");
        
        $user = $this->getMockedUser("foo", "bar");
        
        $store = $this->getMockedStorage();
        $loader = $this->getMockedUserLoader("foo", $user);
        $strategy = $this->getMockedAuthenticateStrategy($user, $user, AuthenticationStrategyInterface::SKIP);
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testExceptionWhenStrategyReturnAnInvalidReturnValue(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        //$this->expectExceptionMessage("Invalid return value on '%s' strategy");
        
        $user = $this->getMockedUser("foo", "bar");
        
        $store = $this->getMockedStorage();
        $loader = $this->getMockedUserLoader("foo", $user);
        $strategy = $this->getMockedAuthenticateStrategy($user, $user, 10);
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->authenticate($user);
    }
    
    /**
     * Get a mocked user storage
     * 
     * @param string[]|null $methods
     *   Methods called once to mock. Set to null to skip method mocking
     * @param UserInterface $user
     *   User to store. Set to null to skip method mocking
     * @param bool[] $exception
     *   If the method can return an exception and must return it. Set to null to skip method mocking
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked user storage
     */
    private function getMockedStorage(
        ?array $methods = null, 
        ?UserInterface $user = null, 
        ?array $exceptions = null): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(UserStorageInteface::class);
        $r_methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder(UserStorageInteface::class)->setMethods($r_methods)->getMock();
        
        if(null !== $methods && null !== $user && $exceptions !== null) {
            $count = \count($methods) - 1;
            for ($i = 0; $i <= $count; $i++) {
                $returnValue = ($exceptions[$i]) ? $this->throwException(new UserNotFoundException()) : $this->returnValue(null);
                $mock
                    ->expects($this->once())
                    ->method($methods[$i])
                    ->with(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUserFactory::createFromUser($user))
                    ->will($returnValue);
            }
        }
        
        return $mock;
    }
    
}
