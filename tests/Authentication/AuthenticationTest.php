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
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\Exception\AuthenticationFailedException;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;
use Zoe\Component\Security\Exception\LogicException;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use ZoeTest\Component\Security\Fixtures\Authentication\UserLoaderFixture;

/**
 * Get Authentication implementation with mock setted
 * 
 * @param MutableUserInterface|null $user
 *   User passed to authentication
 * @param SecurityTestCase $case
 *   SecurityTestCase instance 
 * @param int $strategyResult
 *   Result given by the strategy mocked
 * 
 * @return AuthenticationInterface
 *   Authentication instance with mocked setted into it
 */
function getAuthenticationForTest(
    ?MutableUserInterface& $user, 
    SecurityTestCase $case,
    int $strategyResult): AuthenticationInterface
{
    $user = $case->getMockedUser(MutableUserInterface::class, "foo", true, 2, 2);
    $loader = $case->getMockedUserLoader("foo");
    $loader->method("loadUser")->with($user)->will($case->returnValue($user));
    $strategy = $case->getMockedAuthenticationStrategy($strategyResult, $user);
    
    return new Authentication($loader, $strategy);
}

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
     * @see \Zoe\Component\Security\Authentication\Authentication::switch()
     */
    public function testSwitch(): void
    {
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->setMethods(["loadUser", "identify"])->getMock();
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->setMethods(["process"])->getMock();
        
        $authentication = new Authentication($loader, $strategy);
        $reflection = new \ReflectionClass($authentication);
        $iAuthentication = $authentication->switch(new UserLoaderFixture(), null);
        $iReflection = new \ReflectionClass($iAuthentication);
        $this->assertInstanceOf(AuthenticationInterface::class, $iAuthentication);
        $this->assertInstanceOf(
            UserLoaderInterface::class, 
            $this->reflection_getPropertyValue($authentication, $reflection, "loader"));
        $this->assertInstanceOf(
            AuthenticationStrategyInterface::class, 
            $this->reflection_getPropertyValue($authentication, $reflection, "strategy"));
        
        $this->assertInstanceOf(
            UserLoaderFixture::class,
            $this->reflection_getPropertyValue($iAuthentication, $reflection, "loader"));
        $this->assertInstanceOf(
            AuthenticationStrategyInterface::class,
            $this->reflection_getPropertyValue($iAuthentication, $reflection, "strategy"));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testAuthenticate(): void
    {
        $user = null;
        $authentication = getAuthenticationForTest($user, $this, AuthenticationStrategyInterface::SUCCESS);
        $userAuthenticated = $authentication->authenticate($user);
        $this->assertInstanceOf(StorableUserInterface::class, $userAuthenticated);
        $this->assertSame("foo", $userAuthenticated->getName());
        $this->assertTrue($userAuthenticated->isRoot());
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $userAuthenticated->getRoles());
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $userAuthenticated->getAttributes());
        
        $user = null;
        $authentication = getAuthenticationForTest($user, $this, AuthenticationStrategyInterface::SHUNT_ON_SUCCESS);
        $userAuthenticated = $authentication->authenticate($user);
        $this->assertInstanceOf(StorableUserInterface::class, $userAuthenticated);
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::switch()
     */
    public function testExceptionLoaderAndStrategyAreBothNullDuringSwitch(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("UserLoader and AuthenticationStrategy cannot be both null during switching process");
        
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->setMethods(["loadUser", "identify"])->getMock();
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->setMethods(["process"])->getMock();
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->switch(null, null);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateOnFail(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'foo' cannot be authenticated");
        
        $user = null;
        $authentication = getAuthenticationForTest($user, $this, AuthenticationStrategyInterface::FAIL);
        
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateOnSkip(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'foo' cannot be authenticated");
        
        $user = null;
        $authentication = getAuthenticationForTest($user, $this, AuthenticationStrategyInterface::SKIP);
        
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testExceptionOnInvalidReturnValueFromStrategy(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        
        $user = null;
        $authentication = getAuthenticationForTest($user, $this, 5);
        
        $authentication->authenticate($user);
    }
    
}
