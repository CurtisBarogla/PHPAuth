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

use Zoe\Component\Security\Acl\Entity\Entity;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;
use Zoe\Component\Security\Exception\InvalidEntityException;

/**
 * Generate mocked entity
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityMock extends Mock
{
    
    /**
     * Mock name returned by getName()
     * 
     * @var string
     */
    private $name;
    
    /**
     * Initialize mocked entity
     *
     * @param string $name
     *   Entity name
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(string $name, ?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(Entity::class);
            $methods = $this->reflection_extractMethods($reflection);
            
            $this->mock = $this->getMockBuilder(Entity::class)->setMethods($methods)->disableOriginalConstructor()->getMock();
            $this->name = $name;
    }
    
    /**
     * Initialize a new mocked entity
     *
     * @param string $name
     *   Mocked user name returned by getName()
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     *
     * @return EntityMock
     *   New mocked entity
     */
    public static function initMock(string $name, ?\ReflectionClass& $reflection = null): EntityMock
    {
        return new EntityMock($name, $reflection);
    }
    
    /**
     * Finalize this mocked entity
     *
     * @return Entity
     *   Mocked entity
     */
    public function finalizeMock(): Entity
    {
        return $this->mock;
    }
    
    /**
     * Mock getIterator()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param \Generator $values
     *   Entity values generator
     *
     * @return self
     *   Fluent
     */
    public function mockGetIterator(PhpUnitCallMethod $count, \Generator $values): self
    {
        $mock = function(string $method) use ($count, $values): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($values)); 
        };
        
        return $this->executeMock("getIterator", $mock, null);
    }
    
    /**
     * Mock add()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $name
     *   Entity value name
     * @param array $permissions
     *   Permissions attached
     *
     * @return self
     *   Fluent
     */
    public function mockAdd(PhpUnitCallMethod $count, string $name, array $permissions): self
    {
        $mock = function(string $method) use ($name, $permissions, $count): void {
            $this->mock->expects($count)->method($method)->with($name, $permissions)->will($this->returnSelf());
        };
        
        return $this->executeMock("add", $mock, null);
    }
    
    /**
     * Mock add() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $values
     *   Array values. Index is the entity value and value the permissions to add
     *
     * @return self
     *   Fluent
     */
    public function mockAdd_consecutively(PhpUnitCallMethod $count, array $values): self
    {
        $mock = function(string $method) use ($values, $count): void {
            $args = [];
            $returned = [];
            $loop = 0;
            foreach ($values as $name => $value) {
                $args[$loop][] = $name;
                $args[$loop][] = $value;
                $returned[] = $this->returnSelf();
                $loop++;
            }
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("add", $mock, null);
    }
    
    /**
     * Mock has()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $value
     *   Value name
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockHas(PhpUnitCallMethod $count, string $value, bool $result): self
    {
        $mock = function(string $method) use ($value, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($value)->will($this->returnValue($result));
        };
        
        return $this->executeMock("has", $mock, null);
    }
    
    /**
     * Mock has() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $values
     *   Index is entity value and value the result
     *
     * @return self
     *   Fluent
     */
    public function mockHas_consecutively(PhpUnitCallMethod $count, array $values): self
    {
        $mock = function(string $method) use ($values, $count): void {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($values, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("has", $mock, null);
    }
    
    /**
     * Mock get()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $value
     *   Entity value name
     * @param array|null $permissionsReturned
     *   Permission returned for this value or null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGet(PhpUnitCallMethod $count, string $value, ?array $permissionsReturned): self
    {
        $mock = function(string $method) use ($value, $permissionsReturned, $count): void {
            $return = (null === $permissionsReturned) ? $this->throwException(new InvalidEntityException()) : $this->returnValue($permissionsReturned);
            $this->mock->expects($count)->method($method)->with($value)->will($return);
        };
        
        return $this->executeMock("get", $mock, null);
    }
    
    /**
     * Mock get() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $values
     *   Index is the entity value and value the permissions returned for this entity value or null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGet_consecutively(PhpUnitCallMethod $count, array $values): self
    {
        $mock = function(string $method) use ($values, $count): void {
            $args = [];
            $returned = [];
            foreach ($values as $name => $value) {
                $return = (null === $value) ? $this->throwException(new InvalidEntityException()) : $this->returnValue($value);
                $args[][] = $name;
                $returned[] = $return;
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("get", $mock, null);
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
     * Mock getProcessor()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $processor
     *   Processor name returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetProcessor(PhpUnitCallMethod $count, string $processor): self
    {
        $mock = function(string $method) use ($processor, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($processor));
        };
        
        return $this->executeMock("getProcessor", $mock, null);
    }
    
    /**
     * Mock isEmpty()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockIsEmpty(PhpUnitCallMethod $count, bool $result): self
    {
        $mock = function(string $method) use ($result, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($result));
        };
        
        return $this->executeMock("isEmpty", $mock, null);
    }
    
    /**
     * Mock isEmpty() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param bool ...$results
     *   Varidic results returned
     *
     * @return self
     *   Fluent
     */
    public function mockIsEmpty_consecutively(PhpUnitCallMethod $count, bool ...$results): self
    {
        $mock = function(string $method) use ($results, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$results);   
        };
        
        return $this->executeMock("isEmpty", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' for entity '%s' has been already mocked",
            $method,
            $this->name);
    }

}
