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
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\User;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;

/**
 * SecurityTestCast testcase
 * 
 * @see \ZoeTest\Component\Security\SecurityTestCase
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class SecurityTestCaseTest extends TestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedUser()
     */
    public function testGetMockedUser(): void
    {
        $user = (new SecurityTestCase())->getMockedUser("foo", "bar");
        
        $this->assertSame("foo", $user->getName());
        $this->assertSame("bar", $user->getPassword());
        
        $user = (new SecurityTestCase())->getMockedUser("foo", null);
        $this->assertNull($user->getPassword()); 
    }
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedUserLoader()
     */
    public function testGetMockedUserLoaderWithException(): void
    {
        $this->expectException(UserNotFoundException::class);
        
        $user = new User("foo", "bar");
        $loader = (new SecurityTestCase())->getMockedUserLoader("foo", $user, true);
        $loader->loadUser($user);
    }
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedUserLoader()
     */
    public function testGetMockedUserLoader(): void
    {
        $user1 = new User("foo", "bar");
        $user2 = new User("foo", "bar", ["role1", "role2"], true, ["foo" => "bar"]);
        
        $loader1 = (new SecurityTestCase())->getMockedUserLoader("foo", $user1);
        $this->assertSame($user1, $loader1->loadUser($user1));
        $this->assertSame("foo", $loader1->identify());
        
        $loader2 = (new SecurityTestCase())->getMockedUserLoader("bar", $user1, false, $user2);
        $this->assertSame($user2, $loader2->loadUser($user1));
        $this->assertSame("bar", $loader2->identify());
    }
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedAuthenticationStrategy()
     */
    public function testGetMockedAuthenticationStrategy(): void
    {
        $user1 = new User("foo", "bar");
        $user2 = new User("foo", "bar");
        
        $strategy = (new SecurityTestCase())->getMockedAuthenticateStrategy();
        $this->assertInstanceOf(AuthenticationStrategyInterface::class, $strategy);
        
        $strategy = (new SecurityTestCase())->getMockedAuthenticateStrategy($user1, $user2, AuthenticationStrategyInterface::SUCCESS);
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $strategy->process($user1, $user2));
        
        $strategy = (new SecurityTestCase())->getMockedAuthenticateStrategy($user1, $user2, AuthenticationStrategyInterface::FAIL);
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $strategy->process($user1, $user2));
        
        $strategy = (new SecurityTestCase())->getMockedAuthenticateStrategy($user1, $user2, AuthenticationStrategyInterface::SHUNT_ON_SUCCESS);
        $this->assertSame(AuthenticationStrategyInterface::SHUNT_ON_SUCCESS, $strategy->process($user1, $user2));
        
        $strategy = (new SecurityTestCase())->getMockedAuthenticateStrategy($user1, $user2, AuthenticationStrategyInterface::SKIP);
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user1, $user2));
    }
    
}
