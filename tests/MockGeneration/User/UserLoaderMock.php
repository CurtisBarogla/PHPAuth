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
use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\Exception\User\UserNotFoundException;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;

/**
 * Responsible to mock user loader
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderMock extends MockGeneration
{
    
    /**
     * Initialize a new user loader mocked generation
     * 
     * @param string $mockId
     *   Mock id
     *   
     * @return UserLoaderMock
     *   New user loader mock generation
     */
    public static function init(string $mockId): UserLoaderMock
    {
        return new UserLoaderMock($mockId, UserLoaderInterface::class);
    }
    
    /**
     * Finalize the mocked user loader
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
     * @param MethodCount $count
     *   Called count
     * @param AuthenticationUserInterface $userGiven
     *   Mocked user given to the loader
     * @param AuthenticationUserInterface|null $userReturned
     *   Mocked user returned by the loader. Set to null to simulate an exception
     *
     * @return self
     *   Fluent
     */
    public function mockLoadUser(
        MethodCount $count, 
        AuthenticationUserInterface $userGiven, 
        ?AuthenticationUserInterface $userReturned): self
    {
        $mock = function(string $method) use ($userGiven, $userReturned, $count): void {
            $return = $this->stubThrowableOnNull(new UserNotFoundException($userGiven, UserNotFoundException::LOADER_ERROR_CODE), $userReturned);
            $this->mock->expects($count)->method($method)->with($userGiven)->will($return);
        };
        
        return $this->executeMock("loadUser", $mock);
    }
    
}
