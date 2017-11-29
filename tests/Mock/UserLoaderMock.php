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

use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;

/**
 * Generate mocked user loader
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderMock extends Mock
{
    
    /**
     * Loader identifier
     * 
     * @var string
     */
    private $identifier;
    
    /**
     * Initialize mocked user loader
     *
     * @param string $identifier
     *   Mocked user loader identifier returned by identify()
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(string $identifier, ?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(UserLoaderInterface::class);
        
        $methods = $this->reflection_extractMethods($reflection);
        $this->mock = $this->getMockBuilder(UserLoaderInterface::class)->setMethods($methods)->getMock();
        $this->identifier = $identifier;
    }
    
    /**
     * Initialize mocked user loader
     *
     * @param string $identifier
     *   Mocked user loader identifier returned by identify()
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public static function initMock(string $identifier, ?\ReflectionClass& $reflection = null): UserLoaderMock
    {
        return new UserLoaderMock($identifier, $reflection);
    }
    
    /**
     * Finalize this mocked user loader
     *
     * @return UserLoaderInterface
     *   Mocked user loader
     */
    public function finalizeMock(): UserLoaderInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock loadUser()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param UserInterface $userGiven
     *   User given as parameter
     * @param MutableUserInterface|null $userReturned
     *   User found or null to throw an exception
     *
     * @return self
     *   Fluent
     */
    public function mockLoadUser(PhpUnitCallMethod $count, UserInterface $userGiven, ?MutableUserInterface $userReturned): self
    {
        $mock = function(string $method) use ($userGiven, $userReturned, $count): void {
            $return = (null === $userReturned) ? $this->throwException(new UserNotFoundException()) : $this->returnValue($userReturned);
            $this->mock->expects($count)->method($method)->with($userGiven)->will($return);
        };
        
        return $this->executeMock("loadUser", $mock, null);
    }
    
    /**
     * Mock identify()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     *
     * @return self
     *   Fluent
     */
    public function mockIdentify(PhpUnitCallMethod $count): self
    {
        $mock = function(string $method) use ($count): void {
            $this->mock->expects($count)->method($method)->will($this->returnValue($this->identifier));   
        };
        
        return $this->executeMock("identify", $mock, null);
    }
        
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string
    {
        return \sprintf("This method '%s' for UserLoader '%s' has been alerady mocked",
            $method,
            $this->identifier);
    }
    
}
