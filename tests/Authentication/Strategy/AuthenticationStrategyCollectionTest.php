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

namespace ZoeTest\Component\Security\Authentication\Strategy;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection;
use Zoe\Component\Security\User\Contracts\UserInterface;
use ZoeTest\Component\Security\Mock\AuthenticationStrategyMock;
use ZoeTest\Component\Security\Mock\UserMock;


/**
 * AuthenticationStrategyCollection testcase
 * 
 * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyCollectionTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::add()
     */
    public function testAdd(): void
    {
        $strategy = AuthenticationStrategyMock::initMock("Foo")->finalizeMock();
        
        $collection = new AuthenticationStrategyCollection();
        
        $this->assertNull($collection->add($strategy));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcess(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $strategy1 = AuthenticationStrategyMock::initMock("Foo")->mockProcess($this->atLeastOnce(), $user, $user2, AuthenticationStrategyInterface::SUCCESS)->finalizeMock();      
        $strategy2 = AuthenticationStrategyMock::initMock("Bar")->mockProcess($this->atLeastOnce(), $user, $user2, AuthenticationStrategyInterface::SKIP)->finalizeMock();
        $strategy3 = AuthenticationStrategyMock::initMock("Moz")->mockProcess($this->atLeastOnce(), $user, $user2, AuthenticationStrategyInterface::SKIP)->finalizeMock();
        
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy1);
        $collection->add($strategy2);
        $collection->add($strategy3);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $collection->process($user, $user2));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithPriority(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $strategy1 = AuthenticationStrategyMock::initMock("Foo")->mockProcess($this->never(), $user, $user2, AuthenticationStrategyInterface::FAIL)->finalizeMock();
        $strategy2 = AuthenticationStrategyMock::initMock("Bar")->mockProcess($this->never(), $user, $user2, AuthenticationStrategyInterface::FAIL)->finalizeMock();
        $strategy3 = AuthenticationStrategyMock::initMock("Moz")->mockProcess($this->atLeastOnce(), $user, $user2, AuthenticationStrategyInterface::SHUNT_ON_SUCCESS)->finalizeMock();
    
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy1, -50);
        $collection->add($strategy2, 150);
        $collection->add($strategy3, 258);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $collection->process($user, $user2));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessOnFail(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $strategy1 = AuthenticationStrategyMock::initMock("Foo")->mockProcess($this->atLeastOnce(), $user, $user2, AuthenticationStrategyInterface::FAIL)->finalizeMock();
        $strategy2 = AuthenticationStrategyMock::initMock("Bar")->mockProcess($this->never(), $user, $user2, AuthenticationStrategyInterface::SUCCESS)->finalizeMock();
        $strategy3 = AuthenticationStrategyMock::initMock("Moz")->mockProcess($this->never(), $user, $user2, AuthenticationStrategyInterface::SKIP)->finalizeMock();
        
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy1);
        $collection->add($strategy2);
        $collection->add($strategy3);
        
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $collection->process($user, $user2));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessOnFailWithShuntOnSuccess(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $strategy1 = AuthenticationStrategyMock::initMock("Foo")->mockProcess($this->never(), $user, $user2, AuthenticationStrategyInterface::FAIL)->finalizeMock();
        $strategy2 = AuthenticationStrategyMock::initMock("Bar")->mockProcess($this->never(), $user, $user2, AuthenticationStrategyInterface::SKIP)->finalizeMock();
        $strategy3 = AuthenticationStrategyMock::initMock("Moz")->mockProcess($this->never(), $user, $user2, AuthenticationStrategyInterface::SUCCESS)->finalizeMock();
        $strategy4 = AuthenticationStrategyMock::initMock("Poz")->mockProcess($this->atLeastOnce(), $user, $user2, AuthenticationStrategyInterface::SHUNT_ON_SUCCESS)->finalizeMock();
        
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy1);
        $collection->add($strategy2);
        $collection->add($strategy3);
        $collection->add($strategy4, 150);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $collection->process($user, $user2));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithOnlySkip(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $strategy1 = AuthenticationStrategyMock::initMock("Foo")->mockProcess($this->atLeastOnce(), $user, $user2, AuthenticationStrategyInterface::SKIP)->finalizeMock();
        $strategy2 = AuthenticationStrategyMock::initMock("Bar")->mockProcess($this->atLeastOnce(), $user, $user2, AuthenticationStrategyInterface::SKIP)->finalizeMock();
        
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy1);
        $collection->add($strategy2);
        
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $collection->process($user, $user2));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testExceptionOnInvalidReturnValueStrategy(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $this->expectException(\UnexpectedValueException::class);

        $strategy = AuthenticationStrategyMock::initMock("Bar")->mockProcess($this->atLeastOnce(), $user, $user2, 5)->finalizeMock();
        
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy);
        
        $collection->process($user, $user2);
    }

}
