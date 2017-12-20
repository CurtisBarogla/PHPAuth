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
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use \PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use Zoe\Component\Security\Acl\Resource\ImmutableResourceInterface;
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Exception\Acl\InvalidEntityException;

/**
 * Responsible to mock resource
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceMock extends MockGeneration
{
    
    /**
     * Resource types
     * 
     * @var array
     */
    private const RESOURCE_TYPES = [
        ResourceInterface::class,
        ImmutableResourceInterface::class
    ];
    
    /**
     * Initialize a new resource mocked generation
     *
     * @param string $mockId
     *   Mock id
     * @param string $type
     *   Resource type to mock (ResourceInterface or ImmutableResourceInterface)
     *
     * @return ResourceMock
     *   New resource mock generation
     */
    public static function init(string $mockId, string $type): ResourceMock
    {
        if(!\in_array($type, self::RESOURCE_TYPES))
            throw new \LogicException(\sprintf("Given resource type '%s' is invalid. Use : '%s'",
                $type,
                \implode(" | ", self::RESOURCE_TYPES)));
        
        return new ResourceMock($mockId, $type);
    }
    
    /**
     * Finalize the mocked resource
     *
     * @return ResourceInterface|ImmutableResourceInterface
     *   Mocked resource
     */
    public function finalizeMock(): ResourceInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock getName()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $name
     *   Name returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetName(MethodCount $count, string $name): self
    {
        $mock = function(string $method) use ($name, $count) {
            $this->mock->expects($count)->method($method)->will($this->returnValue($name));
        };
        
        return $this->executeMock("getName", $mock);
    }
    
    /**
     * Mock addPermission()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $permission
     *   Permission name
     *
     * @return self
     *   Fluent
     */
    public function mockAddPermission(MethodCount $count, string $permission): self
    {
        $mock = function(string $method) use ($permission, $count) {
            if($this->objectName === ImmutableResourceInterface::class)
                $this->mock->expects($count)->method($method)->with($permission)->will($this->throwException(new \BadMethodCallException()));
            else
                $this->mock->expects($count)->method($method)->with($permission)->will($this->returnValue(null));
        };
        
        return $this->executeMock("addPermission", $mock);
    }
    
    /**
     * Mock addPermission() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $permissions
     *   Arrays of array containing all params for each call 
     * 
     * @return self
     *   Fluent
     */
    public function mockAddPermission_consecutive(MethodCount $count, array $permissions): self
    {
        $mock = function(string $method) use ($permissions, $count) {
            if($this->objectName === ImmutableResourceInterface::class)
                $this->mock->expects($count)->method($method)->withConsecutive(...$permissions)->will($this->throwException(new \BadMethodCallException()));
            else
                $this->mock->expects($count)->method($method)->withConsecutive(...$permissions)->willReturnOnConsecutiveCalls($this->returnValue(null)); 
        };
        
        return $this->executeMock("addPermission", $mock);
    }
    
    /**
     * Mock getPermissions()
     *
     * @param MethodCount $count
     *   Called count
     * @param array|null $permissions
     *   Permissions to get
     * @param MaskCollection|null $collection
     *   Collection return or null to simulate an exception
     * @param string|null $invalidPermission
     *   Invalid permission name setted into exception. Setted to null by default
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermissions(
        MethodCount $count, 
        ?array $permissions, 
        ?MaskCollection $collection,
        ?string $invalidPermission = null): self
    {
        $mock = function(string $method) use ($permissions, $collection, $invalidPermission, $count) {
            $return = $this->stubThrowableOnNull($this->setExceptionParameter($invalidPermission), $collection);
            $this->mock->expects($count)->method($method)->with($permissions)->will($return); 
        };
        
        return $this->executeMock("getPermissions", $mock);
    }
    
    /**
     * Mock getPermissions() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $permissions
     *   Arrays of array containing all params for each call
     * @param string|null $invalidPermission
     *   Invalid permission name setted into exception
     * @param MaskCollection|null $collections
     *   Variadic collection returned on each call. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermissions_consecutive(
        MethodCount $count, 
        array $permissions,
        ?string $invalidPermission,
        ?MaskCollection ...$collections): self
    {
        $mock = function(string $method) use ($permissions, $invalidPermission, $collections, $count) {
            $return = $this->stubThrowableOnNull($this->setExceptionParameter($invalidPermission), ...$collections);
            $this->mock->expects($count)->method($method)->withConsecutive(...$permissions)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("getPermissions", $mock);
    }
    
    /**
     * Mock getPermission()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $permission
     *   Permission to get
     * @param Mask|null $permissionReturned
     *   Mask permission returned. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermission(
        MethodCount $count, 
        string $permission, 
        ?Mask $permissionReturned): self
    {
        $mock = function(string $method) use ($permission, $permissionReturned, $count) {
            $return = $this->stubThrowableOnNull($this->setExceptionParameter($permission), $permissionReturned);
            $this->mock->expects($count)->method($method)->with($permission)->will($return);
        };
        
        return $this->executeMock("getPermission", $mock);
    }
    
    /**
     * Mock getPermission() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $permissions
     *   Arrays of array containing all params for each call
     * @param string|null $invalidPermission
     *   Invalid permission name setted into exception
     * @param Mask|null ...$permissionsReturned
     *   Variadic masks permission returned. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermission_consecutive(
        MethodCount $count, 
        array $permissions, 
        ?string $invalidPermission,
        ?Mask ...$permissionsReturned): self
    {
        $mock = function(string $method) use ($permissions, $invalidPermission, $permissionsReturned, $count) {
            $return = $this->stubThrowableOnNull($this->setExceptionParameter($invalidPermission), ...$permissionsReturned);
            $this->mock->expects($count)->method($method)->withConsecutive(...$permissions)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("getPermission", $mock);
    }
    
    /**
     * Mock hasPermission()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $permission
     *   Permission name
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockHasPermission(MethodCount $count, string $permission, bool $result): self
    {
        $mock = function(string $method) use ($permission, $result, $count) {
            $this->mock->expects($count)->method($method)->with($permission)->will($this->returnValue($result));
        };
        
        return $this->executeMock("hasPermission", $mock);
    }
    
    /**
     * Mock hasPermission() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $permissions
     *   Arrays of array containing all params for each call
     * @param bool ...$results
     *   Variadic result returned on each call
     *
     * @return self
     *   Fluent
     */
    public function mockHasPermission_consecutive(MethodCount $count, array $permissions, bool ...$results): self
    {
        $mock = function(string $method) use ($permissions, $results, $count) {
            $this->mock->expects($count)->method($method)->withConsecutive(...$permissions)->willReturnOnConsecutiveCalls(...$results);       
        };
        
        return $this->executeMock("hasPermission", $mock);
    }
    
    /**
     * Mock addEntity().
     * Will throw \BadMethodCallException if Resource type given is ImmutableResourceInterface
     *
     * @param MethodCount $count
     *   Called count
     * @param EntityInterface $entity
     *   Entity to add
     *
     * @return self
     *   Fluent
     */
    public function mockAddEntity(MethodCount $count, EntityInterface $entity): self
    {
        $mock = function(string $method) use ($entity, $count) {
            $return = ($this->objectName === ImmutableResourceInterface::class) ? $this->throwException(new \BadMethodCallException()) : $this->returnValue(null);
            $this->mock->expects($count)->method($method)->with($entity)->will($return);
        };
        
        return $this->executeMock("addEntity", $mock);
    }
    
    /**
     * Mock addEntity() with consecutive calls.
     * Will throw \BadMethodCallException if Resource type given is ImmutableResourceInterface
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $permissions
     *   Arrays of array containing all params for each call
     *
     * @return self
     *   Fluent
     */
    public function mockAddEntity_consecutive(MethodCount $count, array $entities): self
    {
        $mock = function(string $method) use ($entities, $count) {
            $values = \array_fill(0, \count($entities), $this->returnValue(null));
            $return = ($this->objectName === ImmutableResourceInterface::class) ? $this->throwException(new \BadMethodCallException()) : $values;
            $this->mock->expects($count)->method($method)->withConsecutive(...$entities)->willReturnOnConsecutiveCalls($return);
        };
        
        return $this->executeMock("addEntity", $mock);
    }
    
    /**
     * Mock getEntities()
     *
     * @param MethodCount $count
     *   Called count
     * @param array|null $entities
     *   Entities returned. Can return null
     *
     * @return self
     *   Fluent
     */
    public function mockGetEntities(MethodCount $count, ?array $entities): self
    {
        $mock = function(string $method) use ($entities, $count) {
            $this->mock->expects($count)->method($method)->will($this->returnValue($entities));
        };
        
        return $this->executeMock("getEntities", $mock);
    }
    
    /**
     * Mock getEntity()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $entity
     *   Entity name
     * @param EntityInterface|null $entityReturned
     *   Entity returned. Set to null ti simultate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetEntity(MethodCount $count, string $entity, ?EntityInterface $entityReturned): self
    {
        $mock = function(string $method) use ($entity, $entityReturned, $count) {
            $return = $this->stubThrowableOnNull(new InvalidEntityException(), $entityReturned);
            $this->mock->expects($count)->method($method)->with($entity)->will($return);
        };
        
        return $this->executeMock("getEntity", $mock);
    }
    
    /**
     * Mock getEntity() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $permissions
     *   Arrays of array containing all params for each call
     * @param EntityInterface|null ... $entitiesReturned
     *   Entity returned on each call. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetEntity_consecutive(MethodCount $count, array $entities, ?EntityInterface ...$entitiesReturned): self
    {
        $mock = function(string $method) use ($entities, $entitiesReturned, $count) {
            $return = $this->stubThrowableOnNull(new InvalidEntityException(), ...$entitiesReturned);
            $this->mock->expects($count)->method($method)->withConsecutive(...$entities)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("getEntity", $mock);
    }
    
    /**
     * Mock getBehaviour()
     *
     * @param MethodCount $count
     *   Called count
     * @param int $behaviour
     *   Resource behaviour returned (Should be only one of the value defined into the ResourceInterface)
     *
     * @return self
     *   Fluent
     */
    public function mockGetBehaviour(MethodCount $count, int $behaviour): self
    {
        $mock = function(string $method) use ($behaviour, $count) {
            $this->mock->expects($count)->method($method)->will($this->returnValue($behaviour)); 
        };
        
        return $this->executeMock("getBehaviour", $mock);
    }
    
    /**
     * Set the invalid permission name into the InvalidPermission exception
     * 
     * @param string|null $invalidPermission
     *   Invalid permission name to set
     * 
     * @return InvalidPermissionException
     *   InvalidPermissionException with invalid permission name setted
     */
    private function setExceptionParameter(?string $invalidPermission): InvalidPermissionException
    {
        $exception = new InvalidPermissionException();
        if(null !== $invalidPermission)
            $exception->setInvalidPermission($invalidPermission);
        
        return $exception;
    }
    
}
