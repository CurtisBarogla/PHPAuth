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
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
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
