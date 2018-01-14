<?php
//StrictType
declare(strict_types = 1);

/*
 * Zoe
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace ZoeTest\Component\Authentication;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Authentication\Authentication;
use Zoe\Component\Authentication\Exception\AuthenticationFailedException;
use Zoe\Component\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\User\AuthenticatedUserInterface;
use Zoe\Component\User\AuthenticationUserInterface;
use Zoe\Component\User\Exception\UserNotFoundException;
use Zoe\Component\User\Loader\UserLoaderInterface;

/**
 * Authentication testcase
 * 
 * @see \Zoe\Component\Authentication\Authentication
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Authentication\Authentication::authenticate()
     */
    public function testAuthenticateSuccess(): void
    {
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loaded = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loaded->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->getMock();
        $loader->expects($this->once())->method("load")->with($user)->will($this->returnValue($loaded));
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $strategy->expects($this->once())->method("setUser")->with($loaded)->will($this->returnValue(null));
        $strategy->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::SUCCESS));
        
        $authentication = new Authentication($strategy, $loader);
        
        $user = $authentication->authenticate($user);
        
        $this->assertInstanceOf(AuthenticatedUserInterface::class, $user);
        $this->assertSame("Foo", $user->getName());
    }
    
    /**
     * @see \Zoe\Component\Authentication\Authentication::via()
     */
    public function testVia(): void
    {
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loaded = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loaded->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $defaultStrategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $defaultStrategy->expects($this->never())->method("process");
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $strategy->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::SUCCESS));
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->getMock();
        $loader->expects($this->once())->method("load")->with($user)->will($this->returnValue($loaded));
        
        $authentication = new Authentication($defaultStrategy, $loader);
        
        $user = $authentication->via($strategy)->authenticate($user);
        
        $this->assertInstanceOf(AuthenticatedUserInterface::class, $user);
        $this->assertSame("Foo", $user->getName());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Authentication\Authentication::authenticate()
     */
    public function testExceptionAuthenticateSkip(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be authenticated");
        
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $strategy->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::SKIP));
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->getMock();
        $loader->expects($this->once())->method("load");
        
        $authentication = new Authentication($strategy, $loader);
        
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Authentication\Authentication::authenticate()
     */
    public function testExceptionAuthenticateError(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be authenticated");
        
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $strategy->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::ERROR));
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->getMock();
        $loader->expects($this->once())->method("load");
        
        $authentication = new Authentication($strategy, $loader);
        
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Authentication\Authentication::authenticate()
     */
    public function testExceptionAuthenticateUserLoaderFailing(): void
    {
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'Foo' has been not found");
        
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->getMock();
        $loader->expects($this->once())->method("load")->with($user)->will($this->throwException(new UserNotFoundException()));
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $strategy->expects($this->never())->method("process");
        
        $authentication = new Authentication($strategy, $loader);
        
        $authentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Authentication\Authentication::authenticate()
     */
    public function testExceptionWhenStrategyReturnsInvalidValue(): void
    {
        $this->expectException(\LogicException::class);
        
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loader = $this->getMockBuilder(UserLoaderInterface::class)->getMock();
        $loader->expects($this->once())->method("load")->with($user);
        $strategy = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $strategy->expects($this->once())->method("process")->will($this->returnValue(100));
        
        $authentication = new Authentication($strategy, $loader);
        
        $authentication->authenticate($user);
    }
    
}
