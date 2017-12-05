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

namespace ZoeTest\Component\Security\Mock;

use Zoe\Component\Security\Acl\Resource\Loader\ResourceLoaderInterface;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\ResourceNotFoundException;

/**
 * Generate mocked resource loader
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceLoaderMock extends Mock
{
    
    
    /**
     * Initialize mocked resource loader
     *
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(ResourceLoaderInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
            
        $this->mock = $this->getMockBuilder(ResourceLoaderInterface::class)->setMethods($methods)->getMock();
    }
    
    /**
     * Initialize a new mocked resource loader
     *
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     *
     * @return ResourceLoaderMock
     *   New mocked resource loader
     */
    public static function initMock(\ReflectionClass& $reflection = null): ResourceLoaderMock
    {
        return new ResourceLoaderMock($reflection);
    }
    
    /**
     * Finalize this mocked resource loader
     *
     * @return ResourceLoaderInterface
     *   Mocked mask
     */
    public function finalizeMock(): ResourceLoaderInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock loadResource()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $resource
     *   Resource name given
     * @param ResourceInterface|null $resourceReturned
     *   Resource returned or null to simulate exceptions
     *
     * @return self
     *   Fluent
     */
    public function mockLoadResource(PhpUnitCallMethod $count, string $resource, ?ResourceInterface $resourceReturned): self
    {
        $mock = function(string $method) use($resource, $resourceReturned, $count): void {
            $return = (null === $resourceReturned) ? $this->throwException(new ResourceNotFoundException()) : $this->returnValue($resourceReturned);
            $this->mock->expects($count)->method($method)->with($resource)->will($return);   
        };
        
        return $this->executeMock("loadResource", $mock, null);
    }
    
    /**
     * Mock register()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $resources
     *   Names returned
     *
     * @return self
     *   Fluent
     */
    public function mockRegister(PhpUnitCallMethod $count, array $resources): self
    {
        $mock = function(string $method) use($resources, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($resources));
        };
        
        return $this->executeMock("register", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' for this resource loader has been already mocked",
            $method);
    }
    
}
