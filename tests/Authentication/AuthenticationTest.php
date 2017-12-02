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
use ZoeTest\Component\Security\Mock\UserMock;
use ZoeTest\Component\Security\Mock\AuthenticationStrategyMock;
use ZoeTest\Component\Security\Mock\UserLoaderMock;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;

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
        $loader = UserLoaderMock::initMock("Foo")->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")->finalizeMock();
        
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
        $userGiven = UserMock::initMock(CredentialUserInterface::class, "Foo")->finalizeMock();
        $userLoaded = UserMock::initMock(MutableUserInterface::class, "Foo")->mockIsRoot($this->once(), true)->finalizeMock();
        $loader = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $userGiven, $userLoaded)->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")
                                        ->mockProcess($this->once(), $userLoaded, $userGiven, AuthenticationStrategyInterface::SUCCESS)
                                    ->finalizeMock();
        
        $authentication = new Authentication($loader, $strategy);
        $authenticatedUser = $authentication->authenticate($userGiven);
        
        $this->assertInstanceOf(StorableUserInterface::class, $authenticatedUser);
        $this->assertTrue($authenticatedUser->isRoot());
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testAuthenticationOnShunt(): void
    {
        $userGiven = UserMock::initMock(CredentialUserInterface::class, "Foo")->finalizeMock();
        $userLoaded = UserMock::initMock(MutableUserInterface::class, "Foo")->mockIsRoot($this->once(), true)->finalizeMock();
        $loader = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $userGiven, $userLoaded)->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")
                                        ->mockProcess($this->once(), $userLoaded, $userGiven, AuthenticationStrategyInterface::SHUNT_ON_SUCCESS)
                                    ->finalizeMock();
        
        $authentication = new Authentication($loader, $strategy);
        $authenticatedUser = $authentication->authenticate($userGiven);
        
        $this->assertInstanceOf(StorableUserInterface::class, $authenticatedUser);
        $this->assertTrue($authenticatedUser->isRoot());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::switch()
     */
    public function testExceptionLoaderAndStrategyAreBothNullDuringSwitch(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("UserLoader and AuthenticationStrategy cannot be both null during switching process");
        
        $loader = UserLoaderMock::initMock("Foo")->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")->finalizeMock();
        
        $authentication = new Authentication($loader, $strategy);
        $authentication->switch(null, null);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateOnFail(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be authenticated");
        
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->mockGetName($this->once())->finalizeMock();
        $loader = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $user, $user)->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")
                            ->mockProcess($this->atLeastOnce(), $user, $user, AuthenticationStrategyInterface::FAIL)
                        ->finalizeMock();
        
        $authentication = new Authentication($loader, $strategy);
        
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateOnSkip(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be authenticated");
        
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->mockGetName($this->once())->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")
                            ->mockProcess($this->once(), $user, $user, AuthenticationStrategyInterface::SKIP)
                            ->finalizeMock();
        $loader = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $user, $user)->finalizeMock();
        
        $authentication = new Authentication($loader, $strategy);
        
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Authentication::authenticate()
     */
    public function testExceptionOnInvalidReturnValueFromStrategy(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->mockGetName($this->once())->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")
                            ->mockProcess($this->once(), $user, $user, 6)
                            ->finalizeMock();
        $loader = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $user, $user)->finalizeMock();
        
        $authentication = new Authentication($loader, $strategy);
        
        $authentication->authenticate($user);
    }
    
}
