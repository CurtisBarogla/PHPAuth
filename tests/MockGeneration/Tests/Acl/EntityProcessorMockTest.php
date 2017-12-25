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

namespace ZoeTest\Component\Security\MockGeneration\Tests\Acl;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\Acl\EntityProcessorMock;
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\Acl\AclUserInterface;

/**
 * EntityProcessorMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityProcessorMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityProcessorMockTest extends TestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityProcessorMock::mockProcess()
     */
    public function testMockProcess(): void
    {
        $entity = EntityMock::init("EntityForMockProcessTest")->finalizeMock();
        $user = UserMock::init("AclUserProcessed", AclUserInterface::class)->finalizeMock();
        $processor = EntityProcessorMock::init("Foo")->mockProcess($this->once(), $entity, $user)->finalizeMock();
        
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\EntityProcessorMock::mockGetIdentifier()
     */
    public function testMockGetIdentifier(): void
    {
        $processor = EntityProcessorMock::init("Foo")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $processor->getIdentifier());
    }
    
}
