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
use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\Exception\User\InvalidUserRoleException;
use Zoe\Component\Security\Exception\User\InvalidUserCredentialException;
use Zoe\Component\Security\User\AuthenticatedUserInterface;

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
        UserInterface::class,
        AuthenticationUserInterface::class,
        AuthenticatedUserInterface::class
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
    
    // Common
    
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
     * Mock isRoot()
     *
     * @param MethodCount $count
     *   Called count
     * @param bool $result
     *   Result
     *
     * @return self
     *   Fluent
     */
    public function mockIsRoot(MethodCount $count, bool $result): self
    {
        $mock = function(string $method) use ($result, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($result));
        };
        
        return $this->executeMock("isRoot", $mock);
    }
    
    /**
     * Mock isRoot() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param bool ...$results
     *   Variadic bool. Result on each call
     *
     * @return self
     *   Fluent
     */
    public function mockIsRoot_consecutive(MethodCount $count, bool ...$results): self
    {
        $mock = function(string $method) use ($results, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$results);
        };
        
        return $this->executeMock("isRoot", $mock);
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
    
    // AuthenticationUser
    
    /**
     * Mock changeName()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $name
     *   New name
     *
     * @return self
     *   Fluent
     */
    public function mockChangeName(MethodCount $count, string $name): self
    {
        $method = "changeName";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($name, $count): void {
            $this->mock->expects($count)->method($method)->with($name)->will($this->returnValue(null));   
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock getPassword()
     *
     * @param MethodCount $count
     *   Called count
     * @param string|null $password
     *   Password returned. Can be null
     *
     * @return self
     *   Fluent
     */
    public function mockGetPassword(MethodCount $count, ?string $password): self
    {
        $method = "getPassword";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($password, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($password));   
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock getPassword() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param string ...$password
     *   Variadic password returned on each call
     *
     * @return self
     *   Fluent
     */
    public function mockGetPassword_consecutive(MethodCount $count, ?string ...$passwords): self
    {
        $method = "getPassword";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($passwords, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$passwords);   
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock addRole()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $role
     *   Role to add
     *
     * @return self
     *   Fluent
     */
    public function mockAddRole(MethodCount $count, string $role): self
    {
        $method = "addRole";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($role, $count): void {
            $this->mock->expects($count)->method($method)->with($role)->will($this->returnValue(null));   
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock addRole() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $roles
     *   Arrays of array containing all params for each call
     *
     * @return self
     *   Fluent
     */
    public function mockAddRole_consecutive(MethodCount $count, array $roles): self
    {
        $method = "addRole";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($roles, $count): void {
            $this->mock->expects($count)->method($method)->withConsecutive(...$roles)->willReturnOnConsecutiveCalls($this->returnValue(null));  
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock deleteRole()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $role
     *   Role to delete
     * @param bool $exception
     *   Set to true to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteRole(MethodCount $count, string $role, bool $exception = false): self
    {
        $method = "deleteRole";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($role, $exception, $count): void {
            $return = $this->stubThrowableOnBool(new InvalidUserRoleException($this->mock, $role), [$this->returnValue(null)], $exception);
            $this->mock->expects($count)->method($method)->with($role)->will($return);
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock deleteRole() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $roles
     *   Arrays of array containing all params for each call
     * @param bool ...$exceptions
     *   True or false to throw exception for each call
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteRole_consecutive(MethodCount $count, array $roles, bool ...$exceptions): self
    {
        $method = "deleteRole";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($roles, $exceptions, $count): void {
            $values = \array_fill(0, \count($roles), $this->returnValue(null));
            $return = $this->stubThrowableOnBool(new InvalidUserRoleException($this->mock, "Foo"), $values, ...$exceptions);
            $this->mock->expects($count)->method($method)
                    ->withConsecutive(...$roles)
                    ->willReturnOnConsecutiveCalls(...$return);   
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock addCredential()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $credential
     *   Credential name
     * @param mixed $value
     *   Credential value
     *
     * @return self
     *   Fluent
     */
    public function mockAddCredential(MethodCount $count, string $credential, $value): self
    {
        $method = "addCredential";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credential, $value, $count): void {
            $this->mock->expects($count)->method($method)->with($credential, $value)->will($this->returnValue(null));
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock addCredential() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array[array] $roles
     *   Arrays of array containing all params for each call
     *
     * @return self
     *   Fluent
     */
    public function mockAddCredential_consecutive(MethodCount $count, array $credentials): self
    {
        $method = "addCredential";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credentials, $count): void {
            $this->mock->expects($count)->method($method)->withConsecutive(...$credentials)->willReturnOnConsecutiveCalls($this->returnValue(null));
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock getCredentials()
     *
     * @param MethodCount $count
     *   Called count
     * @param array $credentials
     *   Credentials returned
     *
     * @return self
     *   Fluent
     */
    public function mockGetCredentials(MethodCount $count, array $credentials): self
    {
        $method = "getCredentials";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credentials, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($credentials));
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock getCredentials() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array ...$credentials
     *   Variadic arrays of credentials returned on each call
     *
     * @return self
     *   Fluent
     */
    public function mockGetCredentials_consecutive(MethodCount $count, array ...$credentials): self
    {
        $method = "getCredentials";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credentials, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$credentials);
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock getCredential()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $credential
     *   Credential name
     * @param mixed|\Throwable $value
     *   Credential value. Set to a \Throwable to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetCredential(MethodCount $count, string $credential, $value): self
    {
        $method = "getCredential";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credential, $value, $count): void {
            $return = $this->stubThrowableOnValue(new InvalidUserCredentialException($this->mock, $credential), $value);
            $this->mock->expects($count)->method($method)->with($credential)->will($return);
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock getCredential() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array $credentials
     *   Arrays of array containing all params for each call
     * @param mixed|\Throwable ...$values
     *   Variadic values to return on each call. Set to a \Throwable to simualte exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetCredential_consecutive(MethodCount $count, array $credentials, ...$values): self
    {
        $method = "getCredential";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credentials, $values, $count): void {
            $return = $this->stubThrowableOnValue(new InvalidUserCredentialException($this->mock, "Foo"), ...$values);
            $this->mock->expects($count)->method($method)->withConsecutive(...$credentials)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock hasCredential()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $credential
     *   Credential name
     * @param bool $result
     *   Result
     *
     * @return self
     *   Fluent
     */
    public function mockHasCredential(MethodCount $count, string $credential, bool $result): self
    {
        $method = "hasCredential";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credential, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($credential)->will($this->returnValue($result));
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock hasCredential() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array $credentials
     *   Arrays of array containing all params for each call
     * @param bool ...$results
     *   Variadic bool. Result on each call
     *
     * @return self
     *   Fluent
     */
    public function mockHasCredential_consecutive(MethodCount $count, array $credentials, bool ...$results): self
    {
        $method = "hasCredential";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credentials, $results, $count): void {
            $this->mock->expects($count)->method($method)->withConsecutive(...$credentials)->willReturnOnConsecutiveCalls(...$results);
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock deleteCredential()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $credential
     *   Credential name
     * @param bool $exception
     *   Set to true to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteCredential(MethodCount $count, string $credential, bool $exception): self
    {
        $method = "deleteCredential";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credential, $exception, $count): void {
            $return = $this->stubThrowableOnBool(
                                new InvalidUserCredentialException($this->mock, $credential), 
                                [$this->returnValue(null)], 
                                $exception);
            $this->mock->expects($count)->method($method)->with($credential)->will($return);
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock deleteCredential() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array $credentials
     *   Arrays of array containing all params for each call
     * @param bool ...$exceptions
     *   Variadic bool. Set to true to simulate exceptions for the current call
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteCredential_consecutive(MethodCount $count, array $credentials, bool ...$exceptions): self
    {
        $method = "deleteCredential";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($credentials, $exceptions, $count): void {
            $values = \array_fill(0, \count($credentials), $this->returnValue(null));
            $return = $this->stubThrowableOnBool(new InvalidUserCredentialException($this->mock, "Foo"), $values, ...$exceptions);
            $this->mock->expects($count)->method($method)->withConsecutive(...$credentials)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Mock deleteCredentials()
     *
     * @param MethodCount $count
     *   Called count
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteCredentials(MethodCount $count): self
    {
        $method = "deleteCredentials";
        $this->checkUser([AuthenticationUserInterface::class], $method);
        $mock = function(string $method) use ($count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue(null));
        };
        
        return $this->executeMock($method, $mock);
    }
    
    // AuthenticatedUser
    
    /**
     * Mock authenticatedAt()
     *
     * @param MethodCount $count
     *   Called count
     * @param \DateTime $time
     *   Time returned
     *
     * @return self
     *   Fluent
     */
    public function mockAuthenticatedAt(MethodCount $count, \DateTime $time): self
    {
        $method = "authenticatedAt";
        $this->checkUser([AuthenticatedUserInterface::class], $method);
        $mock = function(string $method) use ($time, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($time));
        };
        
        return $this->executeMock($method, $mock);
    }
    
    /**
     * Check if a user is valid for a mocked method
     * 
     * @param array $acceptedUsers
     *   Accepted users for mocking this method
     * @param string $method
     *   Mocked method name
     *  
     * @throws \LogicException
     *   If the method cannot be mocked for this user type
     */
    private function checkUser(array $acceptedUsers, string $method): void
    {
        if(!\in_array($this->objectName, $acceptedUsers))
            throw new \LogicException(\sprintf("This user type '%s' for this method '%s' is not valid. Use : '%s'",
                $this->objectName,
                $method,
                \implode(", ", $acceptedUsers)));
    }
    
}
