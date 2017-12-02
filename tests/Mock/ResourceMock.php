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

use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Exception\InvalidResourcePermissionException;
use Zoe\Component\Security\Exception\InvalidEntityException;

/**
 * Generate mocked resource
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceMock extends Mock
{
    
    /**
     * Resource name return by getName()
     * 
     * @var string
     */
    private $name;
    
    /**
     * Initialize mocked resource
     *
     * @param string $name
     *   Resource name
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(string $name, ?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(ResourceInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
            
        $this->mock = $this->getMockBuilder(ResourceInterface::class)->setMethods($methods)->disableOriginalConstructor()->getMock();
        $this->name = $name;
    }
    
    /**
     * Initialize a new mocked resource
     *
     * @param string $name
     *   Mocked resource name returned by getName()
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     *
     * @return ResourceMock
     *   New mocked resource
     */
    public static function initMock(string $name, ?\ReflectionClass& $reflection = null): ResourceMock
    {
        return new ResourceMock($name, $reflection);
    }
    
    /**
     * Finalize this mocked resource
     *
     * @return ResourceInterface
     *   Mocked resource
     */
    public function finalizeMock(): ResourceInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock addPermission()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $permission
     *   Permission name
     *
     * @return self
     *   Fluent
     */
    public function mockAddPermission(PhpUnitCallMethod $count, string $permission): self
    {
        $mock = function(string $method) use ($permission, $count): void {
            $this->mock->expects($count)->method($method)->with($permission)->will($this->returnValue(null));
        };
        
        return $this->executeMock("addPermission", $mock, null);
    }
    
    /**
     * Mock addPermission() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ...string $permissions
     *   Variadic number of permissions
     *
     * @return self
     *   Fluent
     */
    public function mockAddPermission_consecutive(PhpUnitCallMethod $count, string ...$permissions): self
    {
        $mock = function(string $method) use ($permissions, $count): void {
            $args = [];
            $returned = [];
            foreach ($permissions as $permission) {
                $args[][] = $permission;
                $returned[] = $this->returnValue(null);
            }
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("addPermission", $mock, null);
    }
    
    /**
     * Mock getPermission()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array|null $permissions
     *   Permissions to get
     * @param MaskCollection
     *   Permissions as mask collection returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermissions(PhpUnitCallMethod $count, ?array $permissions, MaskCollection $permissionsReturned): self
    {
        $mock = function(string $method) use ($permissions, $permissionsReturned, $count): void {
            $this->mock->expects($count)->method($method)->with($permissions)->will($this->returnValue($permissionsReturned));
        };
        
        return $this->executeMock("getPermissions", $mock, null);
    }
    
    /**
     * Mock getPermission()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $permission
     *   Permission name
     * @param Mask $permissionMask
     *   Mocked mask return or null to throw exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermission(PhpUnitCallMethod $count, string $permission, ?Mask $permissionMask): self
    {
        $mock = function(string $method) use ($permission, $permissionMask, $count): void {
            if(null === $permissionMask)
                $return = $this->throwException(new InvalidResourcePermissionException($this->mock, $permission));
            else
                $return = $this->returnValue($permissionMask);
            
            $this->mock->expects($count)->method($method)->with($permission)->will($return);
        };
        
        return $this->executeMock("getPermission", $mock, null);
    }
    
    /**
     * Mock getPermission() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $permissions
     *   Permission name at index and mocked mask in value or null to throw exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermission_consecutive(PhpUnitCallMethod $count, array $permissions): self
    {
        $mock = function(string $method) use ($permissions, $count): void {
            $args = [];
            $returned = [];
            foreach ($permissions as $permission => $mask) {
                $args[][] = $permission;
                if(null === $mask)
                    $returned[] = $this->throwException(new InvalidResourcePermissionException($this->mock, $permission));
                else 
                    $returned[] = $this->returnValue($mask);
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("getPermission", $mock, null);
    }
    
    /**
     * Mock hasPermission()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $permission
     *   Permission name
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockHasPermission(PhpUnitCallMethod $count, string $permission, bool $result): self
    {
        $mock = function(string $method) use ($permission, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($permission)->will($this->returnValue($result));
        };
        
        return $this->executeMock("hasPermission", $mock, null);
    }
    
    /**
     * Mock hasPermission() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $permissions
     *   Permission name at index bool result for each call
     *
     * @return self
     *   Fluent
     */
    public function mockHasPermission_consecutive(PhpUnitCallMethod $count, array $permissions): self
    {
        $mock = function(string $method) use ($permissions, $count): void {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($permissions, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("hasPermission", $mock, null);
    }
    
    /**
     * Mock addEntity()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param Entity $entity
     *   Mocked entity to add
     *
     * @return self
     *   Fluent
     */
    public function mockAddEntity(PhpUnitCallMethod $count, Entity $entity): self
    {
        $mock = function(string $method) use ($entity, $count): void {
            $this->mock->expects($count)->method($method)->with($entity)->will($this->returnValue(null));
        };
        
        return $this->executeMock("addEntity", $mock, null);
    }
    
    /**
     * Mock addEntity() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param Entity ...$entities
     *   Varidic number of entity to add
     *
     * @return self
     *   Fluent
     */
    public function mockAddEntity_consecutive(PhpUnitCallMethod $count, Entity ...$entities): self
    {
        $mock = function(string $method) use ($entities, $count): void {
            $args = [];
            $returned = [];
            foreach ($entities as $entity) {
                $args[][] = $entity;
                $returned[] = $this->returnValue(null);
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("addEntity", $mock, null);
    }
    
    /**
     * Mock getEntity()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $entity
     *   Entity name
     * @param Entity|null $entityReturned
     *   Mocked entity or null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetEntity(PhpUnitCallMethod $count, string $entity, ?Entity $entityReturned): self
    {
        $mock = function(string $method) use ($entity, $entityReturned, $count): void {
            $return = (null === $entityReturned) ? $this->throwException(new InvalidEntityException()) : $this->returnValue($entityReturned);
            
            $this->mock->expects($count)->method($method)->with($entity)->will($return);
        };
        
        return $this->executeMock("getEntity", $mock, null);
    }
    
    /**
     * Mock getEntity() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $entities
     *   Entity return. Array index is name given as parameters and value is the mocked entity return or null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetEntity_consecutive(PhpUnitCallMethod $count, array $entities): self
    {
        $mock = function(string $method) use ($entities, $count): void {
            $args = [];
            $returned = [];
            foreach ($entities as $name => $entity) {
                $args[][] = $name;
                $returned[] = (null === $entity) ? $this->throwException(new InvalidEntityException()) : $this->returnValue($entity);
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("getEntity", $mock, null);
    }
    
    /**
     * Mock getBehaviour()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param int $behaviour
     *   One of the const declared into ResourceInterface
     *
     * @return self
     *   Fluent
     */
    public function mockGetBehaviour(PhpUnitCallMethod $count, int $behaviour): self
    {
        $mock = function(string $method) use ($behaviour, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($behaviour));
        };
        
        return $this->executeMock("getBehaviour", $mock, null);
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
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' for mocked resource '%s' has been already mocked",
            $method,
            $this->name);
    }
    
}

