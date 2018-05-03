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

namespace ZoeTest\Component\Authentication\Strategy;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Authentication\Strategy\AuthenticationStrategyCollection;
use Zoe\Component\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\User\AuthenticationUserInterface;

/**
 * AuthenticationStrategyCollection testcase
 * 
 * @see \Zoe\Component\Authentication\Strategy\AuthenticationStrategyCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyCollectionTest extends TestCase
{
 
    /**
     * @see \Zoe\Component\Authentication\Strategy\AuthenticationStrategyCollection::add()
     */
    public function testAdd(): void
    {
        $default = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $added = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        
        $strategy = new AuthenticationStrategyCollection($default);
        
        $this->assertNull($strategy->add($added));
    }
    
    /**
     * @see \Zoe\Component\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcess(): void
    {
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $default = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $default->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::SUCCESS));
        $added = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $added->expects($this->never())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::SUCCESS));
        
        $strategy = new AuthenticationStrategyCollection($default);
        $strategy->add($added);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $strategy->process($user));
    }
    
    /**
     * @see \Zoe\Component\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithFail(): void
    {
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $default = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $default->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::ERROR));
        $added = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $added->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::ERROR));
        
        $strategy = new AuthenticationStrategyCollection($default);
        $strategy->add($added);
        
        $this->assertSame(AuthenticationStrategyInterface::ERROR, $strategy->process($user));
    }
    
    /**
     * @see \Zoe\Component\Authentication\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithOnlySkip(): void
    {
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $default = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $default->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::SKIP));
        $added = $this->getMockBuilder(AuthenticationStrategyInterface::class)->getMock();
        $added->expects($this->once())->method("process")->with($user)->will($this->returnValue(AuthenticationStrategyInterface::SKIP));
        
        $strategy = new AuthenticationStrategyCollection($default);
        $strategy->add($added);
        
        $this->assertSame(AuthenticationStrategyInterface::ERROR, $strategy->process($user));
    }
    
}
