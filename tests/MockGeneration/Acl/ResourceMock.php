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

/**
 * Responsible to mock resource
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceMock extends MockGeneration
{
    
    /**
     * Initialize a new resource mocked generation
     *
     * @param string $mockId
     *   Mock id
     *
     * @return ResourceMock
     *   New resource mock generation
     */
    public static function init(string $mockId): ResourceMock
    {
        return new ResourceMock($mockId, ResourceInterface::class);
    }
    
    /**
     * Finalize the mocked resource
     *
     * @return ResourceInterface
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
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermissions(MethodCount $count, ?array $permissions, ?MaskCollection $collection): self
    {
        $mock = function(string $method) use ($permissions, $collection, $count) {
            $return = $this->stubThrowableOnNull(new InvalidPermissionException(), $collection);
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
     * @param MaskCollection|null $collections
     *   Variadic collection returned on each call. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermissions_consecutive(MethodCount $count, array $permissions, ?MaskCollection ...$collections): self
    {
        $mock = function(string $method) use ($permissions, $collections, $count) {
            $return = $this->stubThrowableOnNull(new InvalidPermissionException(), ...$collections);
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
    public function mockGetPermission(MethodCount $count, string $permission, ?Mask $permissionReturned): self
    {
        $mock = function(string $method) use ($permission, $permissionReturned, $count) {
            $return = $this->stubThrowableOnNull(new InvalidPermissionException(), $permissionReturned);
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
     * @param Mask|null ...$permissionsReturned
     *   Variadic masks permission returned. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermission_consecutive(MethodCount $count, array $permissions, ?Mask ...$permissionsReturned): self
    {
        $mock = function(string $method) use ($permissions, $permissionsReturned, $count) {
            $return = $this->stubThrowableOnNull(new InvalidPermissionException(), ...$permissionsReturned);
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
    
}
