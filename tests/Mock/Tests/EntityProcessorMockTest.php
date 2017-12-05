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
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use ZoeTest\Component\Security\Mock\ResourceMock;
use ZoeTest\Component\Security\Mock\EntityMock;
use ZoeTest\Component\Security\Mock\EntityProcessorMock;

/**
 * EntityProcessMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\EntityProcessorMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityProcessorMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityProcessorMock::mockProcessUser()
     */
    public function testMockProcessUser(): void
    {
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->finalizeMock();
        $resource = ResourceMock::initMock("Foo")->finalizeMock();
        $entity = EntityMock::initMock("Foo")->finalizeMock();
        
        $processor = EntityProcessorMock::initMock("Foo")->mockProcessUser($this->once(), $user, $resource, $entity)->finalizeMock();
        $this->assertNull($processor->processUser($user, $resource, $entity));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityProcessorMock::mockGetName()
     */
    public function testMockGetName(): void
    {
        $processor = EntityProcessorMock::initMock("Foo")->mockGetName($this->once())->finalizeMock();
        
        $this->assertSame("Foo", $processor->getName());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityProcessorMock::mockIsProcess()
     */
    public function testMockIsProcess(): void
    {
        $processor = EntityProcessorMock::initMock("Foo")->mockIsProcess($this->once(), true)->finalizeMock();
        
        $this->assertTrue($processor->isProcess());
        
        $processor = EntityProcessorMock::initMock("Foo")->mockIsProcess($this->once(), false)->finalizeMock();
        
        $this->assertFalse($processor->isProcess());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityProcessorMock::mockIsProcess_consecutive()
     */
    public function testMockIsProcess_consecutive(): void
    {
        $processor = EntityProcessorMock::initMock("Foo")->mockIsProcess_consecutive($this->exactly(2), false, true)->finalizeMock();
        
        $this->assertFalse($processor->isProcess());
        $this->assertTrue($processor->isProcess());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\EntityProcessorMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'isProcess' for mock entity processor 'Foo' has been already mocked");
        
        $processor = EntityProcessorMock::initMock("Foo")
                                    ->mockIsProcess($this->once(), true)
                                    ->mockIsProcess($this->once(), false)
                                    ->finalizeMock();
    }
    
}
