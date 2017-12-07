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

use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use PHPUnit_Framework_MockObject_Matcher_Invocation as PhpUnitCallMethod;

/**
 * Generate mocked user
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyMock extends Mock
{
    
    /**
     * Set a mockId for handling error when multiple strategies are mocked on a test
     * 
     * @var string
     */
    private $mockId;
    
    /**
     * Initialize mocked authentication strategy
     *
     * @param string $mockId
     *   Used to identify a strategy over multiple mocked authentication strategy. Never used !
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public function __construct(string $mockId, ?\ReflectionClass& $reflection = null)
    {
        if(null === $reflection)
            $reflection = new \ReflectionClass(AuthenticationStrategyInterface::class);
        
        $methods = $this->reflection_extractMethods($reflection);
        
        $this->mock = $this->getMockBuilder(AuthenticationStrategyInterface::class)->setMethods($methods)->getMock();
        $this->mockId = $mockId;
    }
    
    /**
     * Initialize a new mocked authentication strategy
     *
     * @param string $mockId
     *   Used to identify a strategy over multiple mocked authentication strategy. Never used !
     * @param \ReflectionClass|null $reflection
     *   Reflection class or null (passed by reference)
     */
    public static function initMock(string $mockId, ?\ReflectionClass& $reflection = null): AuthenticationStrategyMock
    {
        return new AuthenticationStrategyMock($mockId, $reflection);
    }
    
    /**
     * Finalize this mocked authentication strategy
     *
     * @return AuthenticationStrategyInterface
     *   Mocked authentication strategy
     */
    public function finalizeMock(): AuthenticationStrategyInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock process()
     * 
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param MutableUserInterface $user1
     *   Mocked user 1 (in the implementation a user from a user loader)
     * @param UserInterface $user2
     *   Mocked user 2
     * @param int $result
     *   Strategy result (one of the value of the interface)
     * 
     * @return self
     *   Fluent
     */
    public function mockProcess(
        PhpUnitCallMethod $count, 
        MutableUserInterface $user1, 
        UserInterface $user2, 
        int $result): self
    {
        $mock = function(string $method) use($user1, $user2, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($user1, $user2)->will($this->returnValue($result));
        };
        
        return $this->executeMock("process", $mock, null);
    }
    
    /**
     * Mock handle()
     *
     * @param \PHPUnit_Framework_MockObject_Matcher_Invocation $count
     *   Number of time called
     * @param MutableUserInterface $userGiven
     *   Mocked user given to the handle process
     * @param MutableUserInterface|null $userReturned
     *   Mocked user handled
     *
     * @return self
     *   Fluent
     */
    public function mockHandle(PhpUnitCallMethod $count, MutableUserInterface $userGiven, ?MutableUserInterface $userReturned): self
    {
        $mock = function(string $method) use ($userGiven, $userReturned, $count): void {
            $this->mock->expects($count)->method($method)->with($userGiven)->will($this->returnValue($userReturned));   
        };
        
        return $this->executeMock("handle", $mock, null);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Mock\Mock::getMessageForExceptionIfMocked()
     */
    protected function getMessageForExceptionIfMocked(string $method): string 
    {
        return \sprintf("This method '%s' for mocked authentication strategy identified by '%s' has been already mocked",
            $method,
            $this->mockId);    
    }
    
}
