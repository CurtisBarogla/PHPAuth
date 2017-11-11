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

namespace ZoeTest\Component\Security;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Internal\ReflectionTrait;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;

/**
 * Common class for Security component testcases
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class SecurityTestCase extends TestCase
{
    
    use ReflectionTrait;
    
    /**
     * Get a mocked user with name and password return by their respective method
     * 
     * @param string $name
     *   Name returned by getName
     * @param string $password
     *   Password returned by getPassword
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked user
     */
    public function getMockedUser(string $name, string $password): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(UserInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder(UserInterface::class)->setMethods($methods)->disableOriginalConstructor()->getMock();
        $mock->method("getName")->will($this->returnValue($name));
        $mock->method("getPassword")->will($this->returnValue($password));
        
        return $mock;
    }
    
    /**
     * Get a mocked user loader.
     * Set userReturned to null to throw the excepted when an user is cannot be loaded
     * 
     * @param UserInterface $user
     *   User to load
     * @param bool $throwException
     *   If the exception must be thrown
     * @param UserInterface $userReturned
     *   User loaded or null to return the first user given
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked UserLoaderInterface with a user return or an exception thrown
     */
    public function getMockedUserLoader(
        UserInterface $user, 
        bool $throwException = false,
        ?UserInterface $userReturned = null): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(UserLoaderInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder(UserLoaderInterface::class)->setMethods($methods)->disableOriginalConstructor()->getMock();
        
        if($throwException) {
            $mock->method("loadUser")->with($user)->will($this->throwException(new UserNotFoundException()));
        } else {
            if(null === $userReturned)
                $mock->method("loadUser")->with($user)->will($this->returnValue($user));
            else
                $mock->method("loadUser")->with($user)->will($this->returnValue($userReturned));
        }
        
        return $mock;
    }
    
    /**
     * Get a mocked authentication strategy
     * Set all parameters to null the get only a mocked instance of it with no process handled
     * 
     * @param UserInterface|null $user1
     *   User 1 passed to process method or null to pass process method mock
     * @param UserInterface|null $user2
     *   User 2 passed to process method or null to pass process method mock
     * @param bool|null $result
     *   Result of the process method or null to pass process method mock
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked authentication strategy
     */
    public function getMockedAuthenticateStrategy(
        ?UserInterface $user1 = null, 
        ?UserInterface $user2 = null,
        ?bool $result = null): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(AuthenticationStrategyInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this
                ->getMockBuilder(AuthenticationStrategyInterface::class)
                ->setMethods($methods)
                ->disableOriginalConstructor()
                ->getMock();
        
        if(null !== $user1 && null !== $user2 && null !== $result)
            $mock->method("process")->with($user1, $user2)->will($this->returnValue($result));
        
        return $mock;
    }
    
}
