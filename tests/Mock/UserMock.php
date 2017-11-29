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
use Zoe\Component\Security\Exception\InvalidUserAttributeException;
use Zoe\Component\Security\User\MutableAclUser;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;
use Zoe\Component\Security\Acl\Mask\Mask;

/**
 * Generate mocked user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserMock extends Mock
{

    /**
     * UserType mocked
     * 
     * @var string
     */
    private $type;
    
    /**
     * Username
     * 
     * @var string
     */
    private $name;
    
    /**
     * User types
     * 
     * @var array
     */
    private const USER_TYPES = [
        UserInterface::class, 
        MutableUserInterface::class,
        StorableUserInterface::class,
        AclUserInterface::class,
        MutableAclUser::class,
        CredentialUserInterface::class
    ];
    
    /**
     * User types considered mutable
     * 
     * @var array
     */
    private const MUTABLES_TYPES = [
        MutableUserInterface::class,
        MutableAclUser::class,
        CredentialUserInterface::class
    ];
    
    /**
     * User types considered acl (and mutable)
     *
     * @var array
     */
    private const ACL_TYPES = [
        AclUserInterface::class,
        MutableAclUser::class
    ];
    
    /**
     * Initialize mocked user
     * 
     * @param string $type
     *   Mocked user type (UserInterface, MutableUserInterface, StorableUserInterface or AclUserInterface)
     * @param string $name
     *   Mocked user name returned by getName()
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     * 
     * @throws \InvalidArgumentException
     *   When given user type is not valid
     */
    public function __construct(string $type, string $name, ?\ReflectionClass& $reflection = null)
    {
        if(!\in_array($type, self::USER_TYPES))
            throw new \InvalidArgumentException(\sprintf("This type '%s' is not valid. Use '%s'",
                $type,
                \implode(" or ", self::USER_TYPES)));
            
        $this->type = $type;
        if(null === $reflection)
            $reflection = new \ReflectionClass($this->type);
        
        $methods = $this->reflection_extractMethods($reflection);
        
        $this->mock = $this->getMockBuilder($this->type)->setMethods($methods)->disableOriginalConstructor()->getMock();
        $this->name = $name;
    }
    
    /**
     * Initialize a new mocked user
     * 
     * @param string $type
     *   Mocked user type (UserInterface, MutableUserInterface, StorableUserInterface or AclUserInterface)
     * @param string $name
     *   Mocked user name returned by getName()
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     * 
     * @return UserMock
     *   New mocked user
     * 
     * @throws \InvalidArgumentException
     *   When given user type is not valid
     */
    public static function initMock(string $type, string $name, ?\ReflectionClass& $reflection = null): UserMock
    {
        return new UserMock($type, $name, $reflection);
    }
    
    /**
     * Finalize this mocked user
     * 
     * @return UserInterface
     *   Mocked user
     */
    public function finalizeMock(): UserInterface
    {
        return $this->mock;
    }
    
    /**
     * COMMON
     */
    
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
     * Mock isRoot()
     * 
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param bool $isRoot
     *   True if root or false
     * 
     * @return self
     *   Fluent
     */
    public function mockIsRoot(PhpUnitCallMethod $count, bool $isRoot): self
    {
        $mock = function(string $method) use ($isRoot, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($isRoot));
        };
        
        return $this->executeMock("isRoot", $mock, null);
    }
    
    /**
     * Mock getRoles()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $roles
     *   Roles to get
     *
     * @return self
     *   Fluent
     */
    public function mockGetRoles(PhpUnitCallMethod $count, array $roles): self
    {
        $mock = function(string $method) use ($roles, $count): void {
            $roles = \array_combine($roles, $roles);
            $this->mock->expects($count)->method($method)->will($this->returnValue($roles));            
        };
        
        return $this->executeMock("getRoles", $mock, null);
    }
    
    /**
     * Mock getRoles() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ...array $roles
     *   Result on each call
     *
     * @return self
     *   Fluent
     */
    public function mockGetRoles_consecutive(PhpUnitCallMethod $count, array ...$consecutiveRolesReturned): self
    {
        $mock = function(string $method) use ($consecutiveRolesReturned, $count): void {            
            foreach ($consecutiveRolesReturned as $index => $role) {
                $consecutiveRolesReturned[$index] = \array_combine($role, $role);
            }
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$consecutiveRolesReturned);
        };
        
        return $this->executeMock("getRoles", $mock, null);
    }
    
    /**
     * Mock getAttributes()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $attributes
     *   Attributes to get
     *
     * @return self
     *   Fluent
     */
    public function mockGetAttributes(PhpUnitCallMethod $count, ?array $attributes): self
    {
        $mock = function(string $method) use ($attributes, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($attributes));
        };
        
        return $this->executeMock("getAttributes", $mock, null);
    }
    
    /**
     * Mock getAttributes() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ...array $attributes
     *   Result on each call
     *
     * @return self
     *   Fluent
     */
    public function mockGetAttributes_consecutive(PhpUnitCallMethod $count, ?array ...$attributes): self
    {
        $mock = function(string $method) use ($attributes, $count): void {
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$attributes);            
        };
        
        return $this->executeMock("getAttributes", $mock, null);
    }
    
    /**
     * Mock getAttribute()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $attributeName
     *   Attribute name to get
     * @param mixed $attribute
     *   Attribute value to get. Can throw exception when instance of \Exception given
     *
     * @return self
     *   Fluent
     */
    public function mockGetAttribute(PhpUnitCallMethod $count, string $attributeName, $attribute): self
    {
        $mock = function(string $method) use ($attributeName, $attribute, $count): void {            
            $result = ($attribute instanceof \Exception) ? $this->throwException($attribute) : $this->returnValue($attribute);
            $this->mock->expects($count)->method($method)->with($attributeName)->will($result);
        };
        
        return $this->executeMock("getAttribute", $mock, null);
    }
    
    /**
     * Mock getAttribute() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ...array $returnedAttributes
     *   Result on each call. Array index determine the attribute name and the value for this index the attribute value
     *
     * @return self
     *   Fluent
     */
    public function mockGetAttribute_consecutive(PhpUnitCallMethod $count, array ...$returnedAttributes): self
    {
        $mock = function(string $method) use ($returnedAttributes, $count): void {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($returnedAttributes, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);                
        };
        
        return $this->executeMock("getAttribute", $mock, null);
    }
    
    /**
     * Mock hasRole()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $role
     *   Role name
     * @param bool $result
     *   True if the user has the role. False otherwise
     *
     * @return self
     *   Fluent
     */
    public function mockHasRole(PhpUnitCallMethod $count, string $role, bool $result): self
    {
        $mock = function(string $method) use ($role, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($role)->will($this->returnValue($result));
            
        };
        
        return $this->executeMock("hasRole", $mock, null);
    }
    
    /**
     * Mock hasRole() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ...array $returnedResults
     *   Result on each call. Array index determine the role name and the value for this index the result
     *
     * @return self
     *   Fluent
     */
    public function mockHasRole_consecutive(PhpUnitCallMethod $count, array ...$returnedResults): self
    {
        $mock = function(string $method) use ($returnedResults, $count): void {            
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($returnedResults, $args, $returned);
            
            $this->mock->expects($count)->method("hasRole")->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("hasRole", $mock, null);
    }
    
    /**
     * Mock hasAttribute()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $attribute
     *   Attribute name
     * @param bool $result
     *   True if the user has the attribute. False otherwise
     *
     * @return self
     *   Fluent
     */
    public function mockHasAttribute(PhpUnitCallMethod $count, string $attribute, bool $result): self
    {
        $mock = function(string $method) use ($attribute, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($attribute)->will($this->returnValue($result));            
        };
        
        return $this->executeMock("hasAttribute", $mock, null);
    }
    
    /**
     * Mock hasAttribute() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ...array $returnedResults
     *   Result on each call. Array index determine the attribute name and the value for this index the result
     *
     * @return self
     *   Fluent
     */
    public function mockHasAttribute_consecutive(PhpUnitCallMethod $count, array ...$returnedResults): self
    {
        $mock = function(string $method) use ($returnedResults, $count): void {            
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($returnedResults, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("hasAttribute", $mock, null);
    }
    
    /**
     * MUTABLE
     */
    
    /**
     * Mock addRole() 
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $roles
     *   Role to add
     *
     * @return self
     *   Fluent
     */
    public function mockAddRole(PhpUnitCallMethod $count, string $role): self
    {
        $mock = function(string $method) use ($role, $count): void {
            $this->mock->expects($count)->method("addRole")->with($role)->will($this->returnSelf());            
        };

        return $this->executeMock("addRole", $mock, self::MUTABLES_TYPES);
    }
    
    /**
     * Mock addRole() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string ...$roles
     *   Variadic roles to add
     *
     * @return self
     *   Fluent
     */
    public function mockAddRole_consecutive(PhpUnitCallMethod $count, string ...$roles): self
    {
        $mock = function(string $method) use ($roles, $count): void {
            $rolesArg = [];
            foreach ($roles as $role) {
                $rolesArg[][] = $role;
                $returned[] = $this->returnSelf();
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$rolesArg)->willReturnOnConsecutiveCalls(...$returned);            
        };
        
        return $this->executeMock("addRole", $mock, self::MUTABLES_TYPES);
    }
    
    /**
     * Mock addAttribute()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $attribute
     *   Attribute name
     * @param mixed $value
     *   Attribute value
     *
     * @return self
     *   Fluent
     */
    public function mockAddAttribute(PhpUnitCallMethod $count, string $attribute, $value): self
    {
        $mock = function(string $method) use ($attribute, $value, $count): void {
            $this->mock->expects($count)->method($method)->with($attribute, $value)->will($this->returnSelf());
            
        };
        
        return $this->executeMock("addAttribute", $mock, self::MUTABLES_TYPES);
    }
    
    /**
     * Mock addRole() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array ...$attributes
     *   Variadic arrays with at index the attribute name and at value the attribute value
     *
     * @return self
     *   Fluent
     */
    public function mockAddAttribute_consecutive(PhpUnitCallMethod $count, array ...$attributes): self
    {
        $mock = function(string $method) use ($attributes, $count): void {            
            $args = [];
            $returned = [];
            $loop = 0;
            foreach ($attributes as $attributesVariadic) {
                foreach ($attributesVariadic as $name => $value) {
                    $args[$loop][] = $name;
                    $args[$loop][] = $value;
                    $returned[] = $this->returnSelf();
                    $loop++;
                }
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);
        };
        
        return $this->executeMock("addAttribute", $mock, self::MUTABLES_TYPES);
    }
    
    /**
     * Mock deleteAttribute()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $attribute
     *   Attribute name to delete
     * @param bool $exception
     *   If exception is throw for simulate error
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteAttribute(PhpUnitCallMethod $count, string $attribute, bool $exception = false): self
    {
        $mock = function(string $method) use ($attribute, $exception, $count): void {            
            $return = 
                ($exception) ? $this->throwException(new InvalidUserAttributeException($this->mock, $attribute)) : $this->returnSelf();
            $this->mock->expects($count)->method($method)->with($attribute)->will($return);
        };
        
        return $this->executeMock("deleteAttribute", $mock, self::MUTABLES_TYPES);
    }
    
    /**
     * Mock addRole() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array ...$attributes
     *   Variadic arrays with at index the attribute name and at a bool value if an exception must be thrown
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteAttribute_consecutive(PhpUnitCallMethod $count, array ...$attributes): self
    {
        $mock = function(string $method) use ($attributes, $count): void {
            $args = [];
            $returned = [];
            foreach ($attributes as $variadicAttributes) {
                foreach ($variadicAttributes as $attribute => $exception) {
                    $args[][] = $attribute;
                    $returned[] = ($exception) 
                                    ? $this->throwException(new InvalidUserAttributeException($this->mock, $attribute)) 
                                    : $this->returnSelf();
                }
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);            
        };
        
        return $this->executeMock("deleteAttribute", $mock, self::MUTABLES_TYPES);
    }
    
    /**
     * CREDENTIAL
     */
    
    /**
     * Mock getPassword()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $password
     *   Password to return
     *
     * @return self
     *   Fluent
     */
    public function mockGetPassword(PhpUnitCallMethod $count, string $password): self
    {
        $mock = function(string $method) use ($password, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($password));            
        };
        
        return $this->executeMock("getPassword", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * Mock getCredentials()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array|null $credentials
     *   Credentials returned. Can be null
     *
     * @return self
     *   Fluent
     */
    public function mockGetCredentials(PhpUnitCallMethod $count, ?array $credentials): self
    {
        $mock = function(string $method) use ($credentials, $count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($credentials));            
        };
        
        return $this->executeMock("getCredentials", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * Mock getCredentials() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array[]|null ...$credentials
     *   Variadic arrays returned at each call. Can be null
     *
     * @return self
     *   Fluent
     */
    public function mockGetCredentials_consecutive(PhpUnitCallMethod $count, ?array ...$credentials): self
    {   
        $mock = function(string $method) use ($credentials, $count): void {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($credentials, $args, $returned);
            
            $this->mock->expects($count)->method($method)->willReturnOnConsecutiveCalls(...$returned);            
        };
        
        return $this->executeMock("getCredentials", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * Mock getCredential()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $credential
     *   Credential name
     * @param string|\Exception $value
     *   Credentials value. String or Exception instance
     *
     * @return self
     *   Fluent
     */
    public function mockGetCredential(PhpUnitCallMethod $count, string $credential, $value): self
    {
        $mock = function(string $method) use ($credential, $value, $count): void {
            if($value instanceof \Exception) {
                $return = $this->throwException($value);
            } else {
                if(!\is_string($value))
                    throw new \LogicException(
                        \sprintf("Value for credential '%s' MUST be an instance of Exception or a string on user '%s'",
                            $credential,
                            $this->name));
                $return = $this->returnValue($value);
            }
            
            $this->mock->expects($count)->method($method)->with($credential)->will($return);            
        };
        
        return $this->executeMock("getCredential", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * Mock getCredential() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $credentials
     *   Credential returned on each call. Index determine credential name and value credential value. Value can be exception or string
     *
     * @return self
     *   Fluent
     */
    public function mockGetCredentical_consecutive(PhpUnitCallMethod $count, array $credentials): self
    {
        $mock = function(string $method) use ($credentials, $count): void {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($credentials, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);            
        };
        
        return $this->executeMock("getCredential", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * Mock hasCredential()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $credential
     *   Credential name
     * @param bool $result
     *   True if credential exists. False otherwise
     *
     * @return self
     *   Fluent
     */
    public function mockHasCredential(PhpUnitCallMethod $count, string $credential, bool $result): self
    {
        $mock = function(string $method) use ($credential, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($credential)->will($this->returnValue($result));            
        };
        
        return $this->executeMock("hasCredential", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * Mock hasCredential() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $credentials
     *   Credential returned on each call. Index determine credential name and value if credential is valid.
     *
     * @return self
     *   Fluent
     */
    public function mockHasCredential_consecutive(PhpUnitCallMethod $count, array $credentials): self
    {
        $mock = function(string $method) use ($credentials, $count): void {
            $args = [];
            $returned = [];
            $this->extractArrayVariadic($credentials, $args, $returned);
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);            
        };
        
        return $this->executeMock("hasCredential", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * Mock addCredential()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $credential
     *   Credential name
     * @param bool $value
     *   Credential value
     *
     * @return self
     *   Fluent
     */
    public function mockAddCredential(PhpUnitCallMethod $count, string $credential, string $value): self
    {
        $mock = function(string $method) use ($credential, $value, $count): void {
            $this->mock->expects($count)->method($method)->with($credential, $value)->will($this->returnSelf());            
        };
        
        return $this->executeMock("addCredential", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * Mock addCredential() with consecutive calls
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param array $credentials
     *   Credential to add on each call. Index determine credential name and value if credential value.
     *
     * @return self
     *   Fluent
     */
    public function mockAddCredential_consecutive(PhpUnitCallMethod $count, array $credentials): self
    {
        $mock = function(string $method) use ($credentials, $count): void {
            $args = [];
            $returned = [];
            $loop = 0;
            foreach ($credentials as $credential => $value) {
                $args[$loop][] = $credential;
                $args[$loop][] = $value;
                $returned[] = $this->returnSelf();
                $loop++;
            }
            
            $this->mock->expects($count)->method($method)->withConsecutive(...$args)->willReturnOnConsecutiveCalls(...$returned);            
        };
        
        return $this->executeMock("addCredential", $mock, [CredentialUserInterface::class]);
    }
    
    /**
     * ACL
     */
    
    /**
     * Mock grant()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ResourceInterface $resource
     *   Mocked resource
     * @param array $permissions
     *   Permissions to pass
     *
     * @return self
     *   Fluent
     */
    public function mockGrant(PhpUnitCallMethod $count, ResourceInterface $resource, array $permissions): self
    {
        $mock = function(string $method) use ($resource, $permissions, $count): void {
            $this->mock->expects($count)->method($method)->with($resource, $permissions)->will($this->returnValue(null));            
        };
        
        return $this->executeMock("grant", $mock, self::ACL_TYPES);
    }
    
    /**
     * Mock deny()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param ResourceInterface $resource
     *   Mocked resource
     * @param array $permissions
     *   Permissions to pass
     *
     * @return self
     *   Fluent
     */
    public function mockDeny(PhpUnitCallMethod $count, ResourceInterface $resource, array $permissions): self
    {
        $mock = function(string $method) use ($resource, $permissions, $count): void {            
            $this->mock->expects($count)->method($method)->with($resource, $permissions)->will($this->returnValue(null));
        };
        
        return $this->executeMock("deny", $mock, self::ACL_TYPES);
    }
    
    /**
     * Mock getPermission()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $permission
     *   Permission name
     * @param Mask|null $mask
     *   Mocked mask or null to throw exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetPermission(PhpUnitCallMethod $count, string $permission, ?Mask $mask): self
    {
        $mock = function(string $method) use($permission, $mask, $count): void {
            $return = (null === $mask) ? $this->throwException(new \Exception()) : $this->returnValue($mask);
            $this->mock->expects($count)->method($method)->with($permission)->will($return);
        };
        
        return $this->executeMock("getPermission", $mock, self::ACL_TYPES);
    }
    
    /**
     * INTERNAL
     */
    
    /**
     * Check a user type over a method
     * 
     * @param array $typesRequired
     *   Types valids for mocking this method
     * @param string $method
     *   Method to mock
     * 
     * @throws \LogicException
     *   When the current instance type is invalid
     */
    private function checkUserType(array $typesRequired, string $method): void
    {
        if(!\in_array($this->type, $typesRequired)) {
            throw new \LogicException(\sprintf("Impossible to mock this method '%s' on this user type '%s'. Use a valid one : '%s'",
                $method,
                $this->type,
                \implode(", ", $typesRequired)));
        }
    }
    
    /**
     * Extra argument will be used for checking if needed a user type
     * 
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::executeMock()
     */
    protected function executeMock(string $method, callable $mock, ...$extra)
    {
        if(null !== $extra[0])
            $this->checkUserType($extra[0], $method);
        
        $this->throwExceptionIfMocked($method);
        
        \call_user_func($mock, $method);

        $this->addMethodMocked($method);
        
        return $this;
    }

    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' for mocked user '%s' has been already mocked", 
            $method,
            $this->name); 
    }
    
}
