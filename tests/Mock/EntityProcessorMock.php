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

use Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Entity\Entity;

/**
 * Generate mocked entity processor
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityProcessorMock extends Mock
{
    
    /**
     * Processor name return by getName()
     * 
     * @var string
     */
    private $name;
    
    /**
     * Initialize mocked entity processor
     *
     * @param string $name
     *   Entity name
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(string $name, ?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(EntityProcessorInterface::class);
            $methods = $this->reflection_extractMethods($reflection);
            
            $this->mock = $this->getMockBuilder(EntityProcessorInterface::class)->setMethods($methods)->disableOriginalConstructor()->getMock();
            $this->name = $name;
    }
    
    /**
     * Initialize a new mocked entity processor
     *
     * @param string $name
     *   Mocked user name returned by getName()
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     *
     * @return EntityProcessorMock
     *   New mocked entity processor
     */
    public static function initMock(string $name, ?\ReflectionClass& $reflection = null): EntityProcessorMock
    {
        return new EntityProcessorMock($name, $reflection);
    }
    
    /**
     * Finalize this mocked entity
     *
     * @return EntityProcessorInterface
     *   Mocked entity processor
     */
    public function finalizeMock(): EntityProcessorInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock processUser()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param AclUserInterface $user
     *   Mocked acl user
     * @param ResourceInterface $resource
     *   Mocked resource
     * @param Entity $entity
     *   Mocked entity
     *
     * @return self
     *   Fluent
     */
    public function mockProcessUser(PhpUnitCallMethod $count, AclUserInterface $user, ResourceInterface $resource, Entity $entity): self
    {
        $mock = function(string $method) use ($user, $resource, $entity, $count): void {
            $this->mock->expects($count)->method($method)->with($user, $resource, $entity)->will($this->returnValue(null));   
        };
        
        return $this->executeMock("processUser", $mock, null);
    }
    
    /**
     * Mock getName()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     *
     * @return self
     *   Fluent
     */
    public function mockGetName(PhpUnitCallMethod $count): self
    {
        $mock = function(string $method) use ($count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($this->name));   
        };
        
        return $this->executeMock("getName", $mock, null);       
    }
    
    /**
     * Mock isProcess()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockIsProcess(PhpUnitCallMethod $count, bool $result): self
    {
        $mock = function(string $method) use ($result, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($result));  
        };
        
        return $this->executeMock("isProcess", $mock, null);
    }
    
    /**
     * Mock isProcess() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param bool ...$results
     *   Varidic results returned on each call
     *
     * @return self
     *   Fluent
     */
    public function mockIsProcess_consecutive(PhpUnitCallMethod $count, bool ...$results): self
    {
        $mock = function(string $method) use ($results, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$results);    
        };
        
        return $this->executeMock("isProcess", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' for mock entity processor '%s' has been already mocked",
            $method,
            $this->name);
    }
    
}
