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
use Zoe\Component\Security\User\Storage\UserStorageInterface;
use \PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;
use Zoe\Component\Security\User\AuthenticatedUserInterface;
use Zoe\Component\Security\Exception\User\UserNotFoundException;

/**
 * Responsible to mock user storage
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserStorageMock extends MockGeneration
{
    
    /**
     * Initialize a new user storage mocked generation
     *
     * @param string $mockId
     *   Mock id
     *
     * @return UserStorageMock
     *   New user storage mock generation
     */
    public static function init(string $mockId): UserStorageMock
    {
        return new UserStorageMock($mockId, UserStorageInterface::class);
    }
    
    /**
     * Finalize the mocked user storage
     *
     * @return UserStorageInterface
     *   Mocked user storage
     */
    public function finalizeMock(): UserStorageInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock addUser()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $identifier
     *   Store identifier
     * @param AuthenticatedUserInterface $user
     *   Mocked user to store
     *
     * @return self
     *   Fluent
     */
    public function mockAddUser(MethodCount $count, string $identifier, AuthenticatedUserInterface $user): self
    {
        $mock = function(string $method) use ($identifier, $user, $count): void {
            $this->mock->expects($count)->method($method)->with($identifier, $user)->will($this->returnValue(null));   
        };
        
        return $this->executeMock("addUser", $mock);
    }
    
    /**
     * Mock addUser() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array $identifiersAndUsers
     *   Variadic arrays for each call (first value = identifier & second value = mocked user)
     *
     * @return self
     *   Fluent
     */
    public function mockAddUser_consecutive(MethodCount $count, array $identifiersAndUsers): self
    {
        $mock = function(string $method) use ($identifiersAndUsers, $count) {
            $return = \array_fill(0, \count($identifiersAndUsers), $this->returnValue(null));
            $this->mock->expects($count)->method($method)->withConsecutive(...$identifiersAndUsers)->willReturnOnConsecutiveCalls($return);
        }; 
        
        return $this->executeMock("addUser", $mock);
    }
    
    /**
     * Mock getUser()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $identifier
     *   Arrays of array containing all params for each call
     * @param AuthenticatedUserInterface|null $user
     *   User returned. Set to null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetUser(MethodCount $count, string $identifier, ?AuthenticatedUserInterface $user): self
    {
        $mock = function(string $method) use ($identifier, $user, $count) {
            $userPlaceholder = UserMock::init("MockedUserException", AuthenticatedUserInterface::class)->finalizeMock();
            $return = $this->stubThrowableOnNull(
                new UserNotFoundException($userPlaceholder, UserNotFoundException::STORAGE_ERROR_CODE), 
                $user);
            $this->mock->expects($count)->method($method)->with($identifier)->will($return);
        };
        
        return $this->executeMock("getUser", $mock);
    }
    
    /**
     * Mock getUser() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array $identifiers
     *   Arrays of array containing all params for each call
     * @param AuthenticatedUserInterface|null ...$users
     *   Variadic user returned on each. Set to null to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockGetUser_consecutive(MethodCount $count, array $identifiers, ?AuthenticatedUserInterface ... $users): self
    {
        $mock = function(string $method) use ($identifiers, $users, $count) {
            $userPlaceholder = UserMock::init("MockedUserException", AuthenticatedUserInterface::class)->finalizeMock();
            $return = $this->stubThrowableOnNull(
                new UserNotFoundException($userPlaceholder, UserNotFoundException::STORAGE_ERROR_CODE), 
                ...$users);
            $this->mock->expects($count)->method($method)->withConsecutive(...$identifiers)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("getUser", $mock);
    }
    
    /**
     * Mock deleteUser()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $identifier
     *   Store identifier
     * @param bool $exception
     *   Set to true to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteUser(MethodCount $count, string $identifier, bool $exception = false): self
    {
        $mock = function(string $method) use ($identifier, $exception, $count) {
            $userPlaceholder = UserMock::init("MockedUserException", AuthenticatedUserInterface::class)->finalizeMock();
            $return = $this->stubThrowableOnBool(
                new UserNotFoundException($userPlaceholder, UserNotFoundException::STORAGE_ERROR_CODE), 
                [$this->returnValue(null)], 
                $exception);
            $this->mock->expects($count)->method($method)->with($identifier)->will($return);
        };
        
        return $this->executeMock("deleteUser", $mock);
    }
    
    /**
     * Mock deleteUser() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array $identifiers
     *   Arrays of array containing all params for each call
     * @param bool ...$exceptions
     *   Variadic bool. Set to true to simulate exceptions for the current call
     *
     * @return self
     *   Fluent
     */
    public function mockDeleteUser_consecutive(MethodCount $count, array $identifiers, bool ...$exceptions): self
    {
        $mock = function(string $method) use ($identifiers, $exceptions, $count) {
            $userPlaceholder = UserMock::init("MockedUserException", AuthenticatedUserInterface::class)->finalizeMock();
            $values = \array_fill(0, \count($identifiers), $this->returnValue(null));
            $return = $this->stubThrowableOnBool(
                new UserNotFoundException($userPlaceholder, UserNotFoundException::STORAGE_ERROR_CODE), 
                $values, 
                ...$exceptions);
            $this->mock->expects($count)->method($method)->withConsecutive(...$identifiers)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("deleteUser", $mock);
    }
    
    /**
     * Mock deleteUser()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $identifier
     *   Store identifier
     * @param AuthenticatedUserInterface $user
     *   Mocked user
     * @param bool $exception
     *   Set to true to simulate exception
     *
     * @return self
     *   Fluent
     */
    public function mockRefreshUser(MethodCount $count, string $identifier, AuthenticatedUserInterface $user, bool $exception = false): self
    {
        $mock = function(string $method) use ($identifier, $user, $exception, $count) {
            $userPlaceholder = UserMock::init("MockedUserException", AuthenticatedUserInterface::class)->finalizeMock();
            $return = $this->stubThrowableOnBool(
                new UserNotFoundException($userPlaceholder, UserNotFoundException::STORAGE_ERROR_CODE), 
                [$this->returnValue(null)], 
                $exception);
            $this->mock->expects($count)->method($method)->with($identifier, $user)->will($return);
        };
        
        return $this->executeMock("refreshUser", $mock);
    }
    
    /**
     * Mock refreshUser() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array $identifiersAndUsers
     *   Arrays of array containing all params for each call
     * @param bool ...$exceptions
     *   Variadic bool. Set to true to simulate exceptions for the current call
     *
     * @return self
     *   Fluent
     */
    public function mockRefreshUser_consecutive(MethodCount $count, array $identifiersAndUsers, bool ...$exceptions): self
    {
        $mock = function(string $method) use($identifiersAndUsers, $exceptions, $count) {
            $userPlaceholder = UserMock::init("MockedUserException", AuthenticatedUserInterface::class)->finalizeMock();
            $values = \array_fill(0, \count($identifiersAndUsers), $this->returnValue(null));
            $return = $this->stubThrowableOnBool(
                new UserNotFoundException($userPlaceholder, UserNotFoundException::STORAGE_ERROR_CODE), 
                $values, 
                ...$exceptions);
            $this->mock->expects($count)->method($method)->withConsecutive(...$identifiersAndUsers)->willReturnOnConsecutiveCalls(...$return);
        };
        
        return $this->executeMock("refreshUser", $mock);
    }
    
    /**
     * Mock isStored()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $identifier
     *   Store identifier
     * @param bool $result
     *   Result
     *
     * @return self
     *   Fluent
     */
    public function mockIsStored(MethodCount $count, string $identifier, bool $result): self
    {
        $mock = function(string $method) use ($identifier, $result, $count) {
            $this->mock->expects($count)->method($method)->with($identifier)->will($this->returnValue($result));   
        };
        
        return $this->executeMock("isStored", $mock);
    }
    
    /**
     * Mock isStore() with consecutive calls
     *
     * @param MethodCount $count
     *   Called count
     * @param array $identifiers
     *   Arrays of array containing all params for each call
     * @param bool ...$results
     *   Variadic bool. Result on each call
     *
     * @return self
     *   Fluent
     */
    public function mockIsStored_consecutive(MethodCount $count, array $identifiers, bool ...$results): self
    {
        $mock = function(string $method) use ($identifiers, $results, $count) {
            $this->mock->expects($count)->method($method)->withConsecutive(...$identifiers)->willReturnOnConsecutiveCalls(...$results);
        };
            
        return $this->executeMock("isStored", $mock);
    }
    
}
