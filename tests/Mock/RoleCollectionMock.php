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

use Zoe\Component\Security\Role\RoleCollection;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;

/**
 * Generate mocked role collection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleCollectionMock extends Mock
{
    
    /**
     * Initialize mocked role collection
     *
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(?\ReflectionClass& $reflection = null)
    {
        if($reflection === null)
            $reflection = new \ReflectionClass(RoleCollection::class);
            
        $method = $this->reflection_extractMethods($reflection);
        
        $this->mock = $this->getMockBuilder(RoleCollection::class)->disableOriginalConstructor()->setMethods($method)->getMock();
    }
    
    /**
     * Initialize a new mocked role collection
     *
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     *
     * @return RoleCollectionMock
     *   New mocked password encoder
     */
    public static function initMock(?\ReflectionClass& $reflection = null): RoleCollectionMock
    {
        return new RoleCollectionMock($reflection);
    }
    
    /**
     * Finalize this mocked role collection
     *
     * @return RoleCollection
     *   Mocked role collection
     */
    public function finalizeMock(): RoleCollection
    {
        return $this->mock;
    }
    
    /**
     * Mock get()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $role
     *   Value to encode
     * @param array $returnedRoles
     *   Roles returned
     *
     * @return self
     *   Fluent
     */
    public function mockGet(PhpUnitCallMethod $count, string $role, array $returnedRoles): self
    {
        $mock = function(string $method) use ($role, $returnedRoles, $count): void {
            $this->mock->expects($count)->method($method)->with($role)->will($this->returnValue($returnedRoles));   
        };
        
        return $this->executeMock("get", $mock, null);
    }
    
    /**
     * Mock get() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ...array $roles
     *   Variadic arrays. Index is role given and value roles returned
     *
     * @return self
     *   Fluent
     */
    public function mockGet_consecutive(PhpUnitCallMethod $count, array ...$roles): self
    {
        $mock = function(string $method) use ($roles, $count): void {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($roles, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("get", $mock, null);
    }
    
    /**
     * Mock has()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $role
     *   Role name
     * @param bool $result
     *   Result
     *
     * @return self
     *   Fluent
     */
    public function mockHas(PhpUnitCallMethod $count, string $role, bool $result): self
    {
        $mock = function(string $method) use ($role, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($role)->will($this->returnValue($result));
        };
        
        return $this->executeMock("has", $mock, null);
    }
    
    /**
     * Mock has() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ...array $roles
     *   Variadic arrays. Index is role given and value result returned
     *
     * @return self
     *   Fluent
     */
    public function mockHas_consecutive(PhpUnitCallMethod $count, array $roles): self
    {
        $mock = function(string $method) use ($roles, $count): void {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($roles, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("has", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' has been already mocked for this mocked role collection",
            $method);
    }
    
}
