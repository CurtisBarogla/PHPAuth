<?php
//StrictType
declare(strict_types = 1);

/*
 * Ness
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
 
namespace NessTest\Component\Authentication;

use Ness\Component\Authentication\Authentication;
use Ness\Component\User\Loader\UserLoaderInterface;
use Ness\Component\Authentication\Strategy\AuthenticationStrategyInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Ness\Component\User\UserInterface;
use Ness\Component\User\User;
use Ness\Component\Authentication\User\AuthenticationUser;
use Ness\Component\Authentication\User\AuthenticatedUserInterface;
use Ness\Component\Authentication\Exception\AuthenticationFailedException;
use Ness\Component\User\Exception\UserNotFoundException;
use Ness\Component\Authentication\Exception\UserCredentialNotFoundException;

/**
 * Authentication testcase
 * 
 * @see \Ness\Component\Authentication\Authentication
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationTest extends AuthenticationTestCase
{
    
    /**
     * @see \Ness\Component\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateWhenSuccess(): void
    {
        $user = new User("Foo");
        $actions = function(MockObject $loader, MockObject $strategy) use ($user): void {
            $authenticationUser = AuthenticationUser::initializeFromUser($user);
            $loader->expects($this->once())->method("loadUser")->with("Foo")->will($this->returnValue($user));
            $strategy->expects($this->once())->method("process")->with($authenticationUser)->will($this->returnValue(true));
            $strategy->expects($this->once())->method("setLoadedUser")->with($authenticationUser);
        };
        
        $authentication = $this->getInitializedAuthentication($actions);
        $authenticated = $authentication->authenticate($user);
        
        $this->assertInstanceOf(AuthenticatedUserInterface::class, $authenticated);
    }
    
    /**
     * @see \Ness\Component\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateWhenCredentialIsMissed(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be authenticated by Authentication component as no strategy can handle it");
        $this->expectExceptionCode(Authentication::STRATEGY_ERROR);
        
        $user = new User("Foo", ["moz" => "poz"], ["ROLE_ADMIN"]);
        $actions = function(MockObject $loader, MockObject $strategy) use ($user): void {
            $authenticationUser = AuthenticationUser::initializeFromUser($user);
            $loader->expects($this->once())->method("loadUser")->with("Foo")->will($this->returnValue($user));
            $strategy->expects($this->once())->method("process")->with($authenticationUser)->will($this->throwException(new UserCredentialNotFoundException()));
            $strategy->expects($this->once())->method("setLoadedUser")->with($authenticationUser);
        };
        
        $authentication = $this->getInitializedAuthentication($actions);
        
        try {
            $authentication->authenticate($user);
        } catch (AuthenticationFailedException $e) {
            $user = $e->getFailedAuthenticatedUser();
            $this->assertInstanceOf(AuthenticatedUserInterface::class, $user);
            $this->assertSame("Foo", $user->getName());
            $this->assertFalse($user->isRoot());
            $this->assertSame(["ROLE_ADMIN"], $user->getRoles());
            $this->assertSame(["moz" => "poz"], $user->getAttributes());
            
            throw $e;
        }
    }
    
    /**
     * @see \Ness\Component\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateWhenError(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be authenticated by Authentication component as given strategy failed");
        $this->expectExceptionCode(Authentication::STRATEGY_FAILED);
        
        $user = new User("Foo", ["foo" => "bar"], ["ROLE_MEMBER"]);
        $actions = function(MockObject $loader, MockObject $strategy) use ($user): void {
            $authenticationUser = AuthenticationUser::initializeFromUser($user);
            $loader->expects($this->once())->method("loadUser")->with("Foo")->will($this->returnValue($user));
            $strategy->expects($this->once())->method("process")->with($authenticationUser)->will($this->returnValue(false));
            $strategy->expects($this->once())->method("setLoadedUser")->with($authenticationUser);
        };
        
        $authentication = $this->getInitializedAuthentication($actions);
        
        try {
            $authentication->authenticate($user);            
        } catch (AuthenticationFailedException $e) {
            $user = $e->getFailedAuthenticatedUser();
            $this->assertInstanceOf(AuthenticatedUserInterface::class, $user);
            $this->assertSame("Foo", $user->getName());
            $this->assertFalse($user->isRoot());
            $this->assertSame(["ROLE_MEMBER"], $user->getRoles());
            $this->assertSame(["foo" => "bar"], $user->getAttributes());
            
            throw $e;
        }
    }
    
    /**
     * @see \Ness\Component\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateWhenLoaderFailedToLoadAnUser(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be authenticated as given UserLoader cannot found one");
        $this->expectExceptionCode(Authentication::USER_NOT_FOUND);
        
        $actions = function(MockObject $loader, MockObject $strategy): void {
            $loader->expects($this->once())->method("loadUser")->with("Foo")->will($this->throwException(new UserNotFoundException()));
            $strategy->expects($this->never())->method("process");
            $strategy->expects($this->never())->method("setLoadedUser");
        };
        
        $authentication = $this->getInitializedAuthentication($actions);
        
        try {
            $authentication->authenticate(new User("Foo", ["bar" => "foo"], ["ROLE_ADMIN"]));
        } catch (AuthenticationFailedException $e) {
            $user = $e->getFailedAuthenticatedUser();
            $this->assertInstanceOf(AuthenticatedUserInterface::class, $user);
            $this->assertSame("Foo", $user->getName());
            $this->assertFalse($user->isRoot());
            $this->assertSame(["ROLE_ADMIN"], $user->getRoles());
            $this->assertSame(["bar" => "foo"], $user->getAttributes());
            
            throw $e;
        }
    }
    
    /**
     * @see \Ness\Component\Authentication\Authentication::isAuthenticated()
     */
    public function testIsAuthenticated(): void
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $authentication = $this->getInitializedAuthentication();
        
        $this->assertFalse($authentication->isAuthenticated($user));
        
        $user = $this->getMockBuilder(AuthenticatedUserInterface::class)->getMock();
        
        $this->assertTrue($authentication->isAuthenticated($user));
    }
    
    /**
     * Get an initialized authentication instance with loader and strategy setted
     * 
     * @param \Closure|null $action
     *   Actions done on the loader and the strategy. Takes as parameters the loader and the strategy
     *   
     * @return Authentication
     *   Authentication with loader and strategy setted
     */
    private function getInitializedAuthentication(?\Closure $action = null): Authentication
    {
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->getMock();
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        
        if(null !== $action) {
            $action->call($this, $loader, $strategy);
        }
        
        return new Authentication($loader, $strategy);
    }
    
}
