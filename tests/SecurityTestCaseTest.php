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
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;

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
        // mutable user
        
        $mock = (new SecurityTestCase())->getMockedUser(MutableUserInterface::class, "foo", false);
        
        $this->assertSame("foo", $mock->getName());
        $this->assertInstanceOf(MutableUserInterface::class, $mock);
        $this->assertFalse($mock->isRoot());
        
        // credential user with attributes, roles, and credentials
        
        $mock = (new SecurityTestCase())->getMockedUser(CredentialUserInterface::class, "foo", true, 3, 3);
        
        $this->assertSame(["foo" => "foo", "bar" => "bar", "poz" => "poz"], $mock->getRoles());
        $this->assertSame(["foo" => "bar", "bar" => "foo", "poz" => "moz"], $mock->getAttributes());
        $this->assertSame(["foo" => "bar", "bar" => "foo", "poz" => "moz"], $mock->getCredentials());
        $this->assertInstanceOf(CredentialUserInterface::class, $mock);
        $this->assertTrue($mock->isRoot());
        
        // storable user with attributes and roles
        
        $mock = (new SecurityTestCase())->getMockedUser(StorableUserInterface::class, "foo", false, 2, 2);
        $this->assertSame(["foo" => "foo", "bar" => "bar"], $mock->getRoles());
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $mock->getAttributes());
        $this->assertInstanceOf(StorableUserInterface::class, $mock);
    }
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedUserLoader()
     */
    public function testGetMockedUserLoader(): void
    {
        $mock = (new SecurityTestCase())->getMockedUserLoader("foo");
        
        $this->assertSame("foo", $mock->identify());
    }
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedAuthenticationStrategy()
     */
    public function testGetMockedAuthenticationStrategy(): void
    {
        $user = (new SecurityTestCase())->getMockedUser(MutableUserInterface::class, "foo");
        $user2 = (new SecurityTestCase())->getMockedUser(MutableUserInterface::class, "bar");
        
        $mock = (new SecurityTestCase())->getMockedAuthenticationStrategy(AuthenticationStrategyInterface::SUCCESS, $user);
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $mock->process($user, $user));
        
        $mock = (new SecurityTestCase())->getMockedAuthenticationStrategy(AuthenticationStrategyInterface::FAIL, $user, $user2);
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $mock->process($user, $user2));
    }
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedMask()
     */
    public function testGetMockedMask(): void
    {
        $mock = (new SecurityTestCase())->getMockedMask("foo", 0x0000);
        
        $this->assertSame("foo", $mock->getIdentifier());
        $this->assertSame(0, $mock->getValue());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedUser()
     */
    public function testExceptionOnInvalidCountOfPlaceholdersAttributes(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Count cannot be > 3. '100' given");
        
        $mock = (new SecurityTestCase())->getMockedUser(StorableUserInterface::class, "foo", true, 100);
    }
    
    /**
     * @see \ZoeTest\Component\Security\SecurityTestCase::getMockedUser()
     */
    public function testExceptionOnInvalidCountOfPlaceholdersRoles(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Count cannot be > 3. '100' given");
        
        $mock = (new SecurityTestCase())->getMockedUser(StorableUserInterface::class, "foo", true, null, 100);
    }
    
}
