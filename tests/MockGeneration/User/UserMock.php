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

namespace ZoeTest\Component\Security\MockGeneration\User;

use ZoeTest\Component\Security\MockGeneration\MockGeneration;
use \PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Exception\User\InvalidUserAttributeException;

/**
 * Responsible to mock user objects
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserMock extends MockGeneration
{
    
    /**
     * User types mockables
     * 
     * @var array
     */
    private const USER_TYPES = [
        UserInterface::class
    ];
    
    /**
     * Initialize a new user mocked generation
     * 
     * @param string $mockId
     *   Mock id
     * @param string $userType
     *   User type to mock
     * 
     * @return UserMock
     *   New user mock generation
     *   
     * @throws \LogicException
     *   When the given user type is invalid
     */
    public static function init(string $mockId, string $userType): UserMock
    {
        if(!\in_array($userType, self::USER_TYPES))
            throw new \LogicException(\sprintf("Given user type '%s' is invalid. Use : '%s'",
                $userType,
                \implode(" | ", self::USER_TYPES)));
        
        return new UserMock($mockId, $userType);
    }
    
    /**
     * Finalize the mocked user
     * 
     * @return UserInterface
     *   Mocked user
     */
    public function finalizeMock(): UserInterface
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
        $mock = function(string $method) use ($name, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($name));   
        };
        
        return $this->executeMock("getName", $mock);
    }
    
    /**
     * Mock addAttribute()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $attribute
     *   Attribute name
     * @param mixed $value
     *   Attribute value
     *
     * @return self
     *   Fluent
     */
    public function mockAddAttribute(MethodCount $count, string $attribute, $value): self
    {
        $mock = function(string $method) use ($attribute, $value, $count): void {
            $this->mock->expects($count)->method($method)->with($attribute, $value)->will($this->returnValue(null));   
        };
        
        return $this->executeMock("addAttribute", $mock);
    }
    
    /**
     * Mock addAttribute() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array ...$attributes
     *   Variadic arrays for each call (first value = attribute name & second value = attribute value)
     *
     * @return self
     *   Fluent
     */
    public function mockAddAttribute_consecutive(MethodCount $count, array ...$attributes): self
    {
        $mock = function(string $method) use ($attributes, $count) {
            $this->mock->expects($count)
                       ->method("addAttribute")
                       ->withConsecutive(...$attributes)
                       ->willReturnOnConsecutiveCalls($this->returnValue(null));
        };
        
        return $this->executeMock("addAttribute", $mock);
    }
    
    /**
     * Mock getAttributes()
     * 
     * @param MethodCount $count
     *   Called count
     * @param array $attributes
     *   Attribute returned
     * 
     * @return self
     *   Fluent
     */
    public function mockGetAttributes(MethodCount $count, array $attributes): self
    {
        $mock = function(string $method) use ($attributes, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($attributes));   
        };
        
        return $this->executeMock("getAttributes", $mock);
    }
    
    /**
     * Mock getAttributes() with consecutive calls
     * 
     * @param MethodCount $count
     *   Called count
     * @param array ...$attributes
     *   Variadic arrays for each call 
     *   
     * @return self
     *   Fluent
     */
    public function mockGetAttributes_consecutive(MethodCount $count, array ...$attributes): self
    {
        $mock = function(string $method) use ($attributes, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$attributes);
        };
        
        return $this->executeMock("getAttributes", $mock);
    }
    
    /**
     * Mock getAttribute()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $attribute
     *   Attribute name
     * @param mixed|\Throwable $value
     *   Value or throwable (no matter the instance) to simulate proper exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetAttribute(MethodCount $count, string $attribute, $value): self
    {
        $mock = function(string $method) use ($attribute, $value, $count): void {
            $value = $this->stubThrowableOnValue(new InvalidUserAttributeException($this->mock, $attribute), $value);
            $this->mock->expects($count)->method($method)->with($attribute)->will($value);            
        };
        
        return $this->executeMock("getAttribute", $mock);
    }
    
    /**
     * Mock getAttribute() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $attributes
     *   Arrays of array containing all params for each call 
     * @param mixed|\Throwable ...$values
     *   Variadic values for each call. Can be a \Throwable to simulate exception
     * 
     * @return self
     *   Fluent
     */
    public function mockGetAttribute_consecutive(MethodCount $count, array $attributes, ...$values): self
    {
        $values = $this->stubThrowableOnValue(new InvalidUserAttributeException($this->mock, "Foo"), ...$values);
        $mock = function(string $method) use ($attributes, $values, $count): void {
            $this->mock->expects($count)->method($method)->withConsecutive(...$attributes)->willReturnOnConsecutiveCalls(...$values);
        };
            
        return $this->executeMock("getAttribute", $mock);
    }
    
    /**
     * Mock hasAttribute()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $attribute
     *   Attribute name
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockHasAttribute(MethodCount $count, string $attribute, bool $result): self
    {
        $mock = function(string $method) use ($attribute, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($attribute)->will($this->returnValue($result));   
        };
        
        return $this->executeMock("hasAttribute", $mock);
    }
    
    /**
     * Mock hasAttribute() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $attributes
     *   Arrays of array containing all params for each call
     * @param bool ...$results
     *   Variadic results for each call
     *
     * @return self
     *   Fluent
     */
    public function mockHasAttribute_consecutive(MethodCount $count, array $attributes, bool ...$results): self
    {
        $mock = function(string $method) use ($attributes, $results, $count): void {
            $this->mock->expects($count)->method($method)->withConsecutive(...$attributes)->willReturnOnConsecutiveCalls(...$results);   
        };
        
        return $this->executeMock("hasAttribute", $mock);
    }
    
    /**
     * Mock getRoles()
     *
     * @param MethodCount $count
     *   Called count
     * @param array $roles
     *   Roles returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetRoles(MethodCount $count, array $roles): self
    {
        $mock = function(string $method) use ($roles, $count): void {
            $roles = \array_combine($roles, $roles);
            $this->mock->expects($count)->method($method)->will($this->returnValue($roles));  
        };
        
        return $this->executeMock("getRoles", $mock);
    }
    
    /**
     * Mock getRoles() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array ...$roles
     *   Variadics array of roles for each call
     *
     * @return self
     *   Fluent
     */
    public function mockGetRoles_consecutive(MethodCount $count, array ...$roles): self
    {
        $mock = function(string $method) use ($roles, $count): void {
            foreach ($roles as $index => $role) {
                $roles[$index] = \array_combine($role, $role);
            }
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$roles);
        };
        
        return $this->executeMock("getRoles", $mock);
    }
    
    /**
     * Mock hasRole()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $role
     *   Role name
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockHasRole(MethodCount $count, string $role, bool $result): self
    {
        $mock = function(string $method) use ($role, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($role)->will($this->returnValue($result));
        };
        
        return $this->executeMock("hasRole", $mock);
    }
    
    /**
     * Mock hasRole() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $roles
     *   Arrays of array containing all params for each call
     * @param bool ...$results
     *   Results for each call
     *
     * @return self
     *   Fluent
     */
    public function mockHasRole_consecutive(MethodCount $count, array $roles, bool ...$results): self
    {
        $mock = function(string $method) use ($roles, $results, $count): void {
            $this->mock->expects($count)->method($method)->withConsecutive(...$roles)->willReturnOnConsecutiveCalls(...$results);   
        };
        
        return $this->executeMock("hasRole", $mock);
    }
    
}
