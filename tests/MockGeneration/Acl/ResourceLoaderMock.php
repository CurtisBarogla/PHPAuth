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
use Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface;
use \PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\Acl\ResourceNotFoundException;

/**
 * Responsible to mock resource loader
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceLoaderMock extends MockGeneration
{
    
    /**
     * Initialize a new mask mocked generation
     *
     * @param string $mockId
     *   Mock id
     *
     * @return ResourceLoaderMock
     *   New resource loader mock generation
     */
    public static function init(string $mockId): ResourceLoaderMock
    {
        return new ResourceLoaderMock($mockId, ResourceLoaderInterface::class);
    }
    
    /**
     * Finalize the mocked resource loader
     *
     * @return ResourceLoaderInterface
     *   Mocked resource loader
     */
    public function finalizeMock(): ResourceLoaderInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock loadResource()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $resource
     *   Resource name
     * @param ResourceInterface|null $resourceLoaded
     *   Resource loaded. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockLoadResource(MethodCount $count, string $resource, ?ResourceInterface $resourceLoaded): self
    {
        $mock = function(string $method) use ($resource, $resourceLoaded, $count): void {
            $return = $this->stubThrowableOnNull(new ResourceNotFoundException(), $resourceLoaded);
            $this->mock->expects($count)->method($method)->with($resource)->will($return); 
        };
        
        return $this->executeMock("loadResource", $mock);
    }
    
}
