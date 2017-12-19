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
     * Mock add()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $value
     *   Value to add
     * @param array $permissions
     *   Permissions for this value
     * @param bool $exceptionOnImmutable
     *   Set to true to simulate exception from an immutable state
     *
     * @return self
     *   Fluent
     */
    public function mockAdd(MethodCount $count, string $value, array $permissions, bool $exceptionOnImmutable = false): self
    {
        $mock = function(string $method) use ($value, $permissions, $exceptionOnImmutable, $count): void {
            $return = $this->stubThrowableOnBool(new \BadMethodCallException(), [$this->returnValue(null)], $exceptionOnImmutable);
            $this->mock->expects($count)->method($method)->with($value, $permissions)->will($return);
        };
        
        return $this->executeMock("add", $mock);
    }
    
    /**
     * Mock add() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $valuesAndPermissions
     *   Arrays of array containing all params for each call
     * @param bool ...$exceptionsOnImmutable
     *   If an exception must be thrown on a call
     *
     * @return self
     *   Fluent
     */
    public function mockAdd_consecutive(MethodCount $count, array $valuesAndPermissions, bool ...$exceptionsOnImmutable): self
    {
        $mock = function(string $method) use ($valuesAndPermissions, $exceptionsOnImmutable, $count): void {
            $values = \array_fill(0, \count($valuesAndPermissions), $this->returnValue(null));
            $return = $this->stubThrowableOnBool(new \BadMethodCallException(), $values, ...$exceptionsOnImmutable);
            $this->mock->expects($count)->method($method)->withConsecutive(...$valuesAndPermissions)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("add", $mock);
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
            $return = $this->stubThrowableOnNull(new InvalidEntityValueException(), $permissionsReturned);
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
     * @param array|null ... $permissionsReturned
     *   Permissions returned on each call. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGet_consecutive(MethodCount $count, array $values, ?array ...$permissionsReturned): self
    {
        $mock = function(string $method) use ($values, $permissionsReturned, $count): void {
            $return = $this->stubThrowableOnNull(new InvalidEntityValueException(), ...$permissionsReturned);
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
    
}
