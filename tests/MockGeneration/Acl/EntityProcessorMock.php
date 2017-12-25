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

namespace ZoeTest\Component\Security\MockGeneration\Acl;

use ZoeTest\Component\Security\MockGeneration\MockGeneration;
use Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface;
use \PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Acl\AclUserInterface;

/**
 * Responsible to mock entity processor
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityProcessorMock extends MockGeneration
{
    
    /**
     * Initialize a new entity processor mocked generation
     *
     * @param string $mockId
     *   Mock id
     *
     * @return EntityProcessorMock
     *   New entity processor mock generation
     */
    public static function init(string $mockId): EntityProcessorMock
    {
        return new EntityProcessorMock($mockId, EntityProcessorInterface::class);
    }
    
    /**
     * Finalize the mocked entity processor
     *
     * @return EntityProcessorInterface
     *   Mocked entity processor
     */
    public function finalizeMock(): EntityProcessorInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock process()
     *
     * @param MethodCount $count
     *   Called count
     * @param EntityInterface $entity
     *   Entity processed
     * @param AclUserInterface $user
     *   Acl user processed
     *
     * @return self
     *   Fluent
     */
    public function mockProcess(MethodCount $count, EntityInterface $entity, AclUserInterface $user): self
    {
        $mock = function(string $method) use ($entity, $user, $count): void {
            $this->mock->expects($count)->method($method)->with($entity, $user)->will($this->returnValue(null));   
        };
        
        return $this->executeMock("process", $mock);
    }
    
    /**
     * Mock getIdentifier()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $identifier
     *   Identifier returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetIdentifier(MethodCount $count, string $identifier): self
    {
        $mock = function(string $method) use ($identifier, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($identifier)); 
        };
        
        return $this->executeMock("getIdentifier", $mock);
    }
    
}
