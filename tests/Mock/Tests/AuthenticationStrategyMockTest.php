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

namespace ZoeTest\Component\Security\Mock\Tests;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\AuthenticationStrategyMock;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;

/**
 * AuthenticationStrategyMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\AuthenticationStrategyMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\AuthenticationStrategyMock::mockProcess()
     */
    public function testMockProcess(): void
    {
        $user1 = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")
                                ->mockProcess($this->once(), $user1, $user2, AuthenticationStrategyInterface::SKIP)
                                ->finalizeMock();
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user1, $user2));
    }
    
                    /**_____EXCEPTIONS_____**/  
    
    /**
     * @see \ZoeTest\Component\Security\Mock\AuthenticationStrategyMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'process' for mocked authentication strategy identified by 'Foo' has been already mocked");
        
        $user1 = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $strategy = AuthenticationStrategyMock::initMock("Foo")
                            ->mockProcess($this->any(), $user1, $user2, AuthenticationStrategyInterface::FAIL)
                            ->mockProcess($this->any(), $user1, $user2, AuthenticationStrategyInterface::FAIL)
                            ->finalizeMock();
    }
    
}
