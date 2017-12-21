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
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Exception\Acl\InvalidEntityValueException;
use PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * Responsible to mock entity
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class EntityMock extends MockGeneration
{
    
    /**
     * Initialize a new entity mocked generation
     *
     * @param string $mockId
     *   Mock id
     *
     * @return EntityMock
     *   New entity mock generation
     */
    public static function init(string $mockId): EntityMock
    {
        return new EntityMock($mockId, EntityInterface::class);
    }
    
    /**
     * Finalize the mocked entity
     *
     * @return EntityInterface
     *   Mocked entity
     */
    public function finalizeMock(): EntityInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock getIterator()
     *
     * @param MethodCount $count
     *   Called count
     * @param \Generator $values
     *   Values returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetIterator(MethodCount $count, \Generator $values): self
    {
        $mock = function(string $method) use ($values, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($values));
        };
        
        return $this->executeMock("getIterator", $mock);
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
    
    /**
     * Mock get()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $value
     *   Value to get
     * @param array $permissionsReturned
     *   Permissions returned for this value. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGet(MethodCount $count, string $value, ?array $permissionsReturned): self
    {
        $mock = function(string $method) use ($value, $permissionsReturned, $count): void {
            $return = $this->stubThrowableOnNull($this->setExceptionParameter($value), $permissionsReturned);
            $this->mock->expects($count)->method($method)->with($value)->will($return);
        };
        
        return $this->executeMock("get", $mock);
    }
    
    /**
     * Mock get() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $values
     *   Arrays of array containing all params for each call
     * @param string|null $invalidValue
     *   Invalid value setted into the exception.
     * @param array|null ... $permissionsReturned
     *   Permissions returned on each call. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGet_consecutive(MethodCount $count, array $values, ?string $invalidValue, ?array ...$permissionsReturned): self
    {
        $mock = function(string $method) use ($values, $invalidValue, $permissionsReturned, $count): void {
            $return = $this->stubThrowableOnNull($this->setExceptionParameter($invalidValue), ...$permissionsReturned);
            $this->mock->expects($count)->method($method)->withConsecutive(...$values)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("get", $mock);
    }
    
    /**
     * Mock has()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $value
     *   Value to check
     * @param bool $result
     *   Result for this value
     *
     * @return self
     *   Fluent
     */
    public function mockHas(MethodCount $count, string $value, bool $result): self
    {
        $mock = function(string $method) use ($value, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($value)->will($this->returnValue($result));
        };
        
        return $this->executeMock("has", $mock);
    }
    
    /**
     * Mock has() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $values
     *   Arrays of array containing all params for each call
     * @param bool ...$results
     *   Results for each call
     *
     * @return self
     *   Fluent
     */
    public function mockHas_consecutive(MethodCount $count, array $values, bool ...$results): self
    {
        $mock = function(string $method) use ($values, $results, $count): void {
            $this->mock->expects($count)->method($method)->withConsecutive(...$values)->willReturnOnConsecutiveCalls(...$results);
        };
        
        return $this->executeMock("has", $mock);
    }
    
    /**
     * Mock isEmpty()
     *
     * @param MethodCount $count
     *   Called count
     * @param bool $result
     *   Result
     *
     * @return self
     *   Fluent
     */
    public function mockIsEmpty(MethodCount $count, bool $result): self
    {
        $mock = function(string $method) use ($result, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($result));
        };
        
        return $this->executeMock("isEmpty", $mock);
    }
    
    /**
     * Mock isEmpty() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param bool ...$results
     *   Result on each call
     *
     * @return self
     *   Fluent
     */
    public function mockIsEmpty_consecutive(MethodCount $count, bool ...$results): self
    {
        $mock = function(string $method) use ($results, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$results);
        };
        
        return $this->executeMock("isEmpty", $mock);
    }
    
    /**
     * Mock getProcessor()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $processor
     *   Processor returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetProcessor(MethodCount $count, ?string $processor): self
    {
        $mock = function(string $method) use ($processor, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($processor));
        };
        
        return $this->executeMock("getProcessor", $mock);
    }
    
    /**
     * Mock getResource()
     *
     * @param MethodCount $count
     *   Called count
     * @param ResourceInterface $resource
     *   Resource returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetResource(MethodCount $count, ResourceInterface $resource): self
    {
        $mock = function(string $method) use ($resource, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($resource)); 
        };
        
        return $this->executeMock("getResource", $mock);
    }
    
    /**
     * Set the invalid value name into the InvalidEntityValue exception
     *
     * @param string|null $invalidValue
     *   Invalid value to set
     *
     * @return InvalidEntityValueException
     *   InvalidEntityValueException with invalid value setted
     */
    private function setExceptionParameter(?string $invalidValue): InvalidEntityValueException
    {
        $exception = new InvalidEntityValueException();
        if(null !== $invalidValue)
            $exception->setInvalidValue($invalidValue);
        
        return $exception;
    }
    
}
