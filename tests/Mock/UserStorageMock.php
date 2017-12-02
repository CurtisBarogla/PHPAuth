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

use Zoe\Component\Security\Storage\UserStorageInteface;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;

/**
 * Generate mocked user storage
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserStorageMock extends Mock
{
    
    /**
     * Initialize mocked mask
     * 
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(UserStorageInteface::class);
        $methods = $this->reflection_extractMethods($reflection);
            
        $this->mock = $this->getMockBuilder(UserStorageInteface::class)->setMethods($methods)->getMock();
    }
    
    /**
     * Initialize a new mocked user storage

     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     *
     * @return UserStorageMock
     *   New mocked user storage
     */
    public static function initMock(?\ReflectionClass& $reflection = null): UserStorageMock
    {
        return new UserStorageMock($reflection);
    }
    
    /**
     * Finalize this mocked user storage
     *
     * @return UserStorageInteface
     *   Mocked user storage
     */
    public function finalizeMock(): UserStorageInteface
    {
        return $this->mock;
    }
    
    /**
     * Mock addUser()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $identifier
     *   User identifier
     * @param StorableUserInterface $user
     *   Storable mocked user
     *
     * @return self
     *   Fluent
     */
    public function mockAddUser(PhpUnitCallMethod $count, string $identifier, StorableUserInterface $user): self
    {
        $mock = function(string $method) use ($identifier, $user, $count): void {
            $this->mock->expects($count)->method($method)->with($identifier, $user)->will($this->returnValue(null));
        };
        
        return $this->executeMock("addUser", $mock, null);
    }
    
    /**
     * Mock getUser()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $identifier
     *   User identifier
     * @param StorableUserInterface|null $userReturned
     *   Storable mocked user returned or null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetUser(PhpUnitCallMethod $count, string $identifier, ?StorableUserInterface $userReturned): self
    {
        $mock = function(string $method) use ($identifier, $userReturned, $count): void {
            $return = (null === $userReturned) ? $this->throwException(new UserNotFoundException()) : $this->returnValue($userReturned);
            
            $this->mock->expects($count)->method($method)->with($identifier)->will($return);
        };
        
        return $this->executeMock("getUser", $mock, null);
    }
    
    /**
     * Mock deleteUser()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $identifier
     *   User identifier
     * @param bool $exception
     *   Set to true to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteUser(PhpUnitCallMethod $count, string $identifier, bool $exception): self
    {
        $mock = function(string $method) use ($identifier, $exception, $count): void {
            $return = ($exception) ? $this->throwException(new UserNotFoundException()) : $this->returnValue(null);
            $this->mock->expects($count)->method($method)->with($identifier)->will($return);
        };
        
        return $this->executeMock("deleteUser", $mock, null);
    }
    
    /**
     * Mock refreshUser()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $identifier
     *   User identifier
     * @param StorableUserInterface|null $user
     *   Storable mocked user or null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockRefreshUser(PhpUnitCallMethod $count, string $identifier, ?StorableUserInterface $user): self
    {
        $mock = function(string $method) use ($identifier, $user, $count): void {
            $return = (null === $user) ? $this->throwException(new UserNotFoundException()) : $this->returnValue(null);

            $this->mock->expects($count)->method($method)->with($identifier)->will($return);
        };
        
        return $this->executeMock("refreshUser", $mock, null);
    }
    
    /**
     * Mock hasUser()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param string $identifier
     *   User identifier
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockHasUser(PhpUnitCallMethod $count, string $identifier, bool $result): self
    {
        $mock = function(string $method) use ($identifier, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($identifier)->will($this->returnValue($result)); 
        };
        
        return $this->executeMock("hasUser", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' for thos mocked user storage has been already mocked",
            $method);   
    }

    
}
