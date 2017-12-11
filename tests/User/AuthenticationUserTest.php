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

namespace ZoeTest\Component\Security\User;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\User\AuthenticationUser;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\Exception\User\InvalidUserRoleException;
use Zoe\Component\Security\Exception\User\InvalidUserCredentialException;

/**
 * AuthenticationUser testcase
 * 
 * @see \Zoe\Component\Security\User\AuthenticationUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationUserTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser
     */
    public function testInterface(): void
    {
        $user = new AuthenticationUser("Foo", null);
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(AuthenticationUserInterface::class, $user);
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::changeName()
     */
    public function testChangeName(): void
    {
        $user = new AuthenticationUser("Foo", null);
        
        $this->assertNull($user->changeName("Bar"));
        $this->assertSame("Bar", $user->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::getPassword()
     */
    public function testGetPassword(): void
    {
        $user = new AuthenticationUser("Foo", null);
        
        $this->assertNull($user->getPassword());
        
        $user = new AuthenticationUser("Foo", "Bar");
        
        $this->assertSame("Bar", $user->getPassword());
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::addRole()
     */
    public function testAddRole(): void
    {
        $user = new AuthenticationUser("Foo", null);
        
        $this->assertNull($user->addRole("Foo"));
        $this->assertTrue($user->hasRole("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::deleteRole()
     */
    public function testDeleteRole(): void
    {
        $user = new AuthenticationUser("Foo", null, [], ["Foo"]);
        
        $this->assertTrue($user->hasRole("Foo"));
        $this->assertNull($user->deleteRole("Foo"));
        $this->assertFalse($user->hasRole("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::addCredential()
     */
    public function testAddCredential(): void
    {
        $user = new AuthenticationUser("Foo", null);
        $this->assertNull($user->addCredential("Foo", "Bar"));
        $this->assertSame("Bar", $user->getCredential("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::getCredentials()
     */
    public function testGetCredentials(): void
    {
        $user = new AuthenticationUser("Foo", null, [], [], ["Foo" => "Bar"]);
        
        $this->assertSame(["Foo" => "Bar", "USER_PASSWORD" => null], $user->getCredentials());
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::getCredential()
     */
    public function testGetCredential(): void
    {
        $user = new AuthenticationUser("Foo", null, [], [], ["Foo" => "Bar"]);
        
        $this->assertSame("Bar", $user->getCredential("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::hasCredential()
     */
    public function testHasCredential(): void
    {
        $user = new AuthenticationUser("Foo", null, [], [], ["Foo" => "Bar"]);
        
        $this->assertTrue($user->hasCredential("Foo"));
        $this->assertFalse($user->hasCredential("Bar"));
        $this->assertFalse($user->hasCredential("USER_PASSWORD"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::deleteCredentials()
     */
    public function testDeleteCredentials(): void
    {
        $user = new AuthenticationUser("Foo", null, [], [], ["Foo" => "Bar"]);
        
        $this->assertSame(["Foo" => "Bar", "USER_PASSWORD" => null], $user->getCredentials());
        $this->assertNull($user->deleteCredentials());
        $this->assertEmpty($user->getCredentials());
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::deleteCredential()
     */
    public function testDeleteCredential(): void
    {
        $user = new AuthenticationUser("Foo", null, [], [], ["Foo" => "Bar"]);
        
        $this->assertTrue($user->hasCredential("Foo"));
        $this->assertNull($user->deleteCredential("Foo"));
        $this->assertFalse($user->hasCredential("Foo"));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::deleteRole()
     */
    public function testExceptionDeleteRoleOnInvalidRole(): void
    {
        $this->expectException(InvalidUserRoleException::class);
        $this->expectExceptionMessage("This role 'Bar' for user 'Foo' is not setted");
        
        $user = new AuthenticationUser("Foo", null);
        
        $user->deleteRole("Bar");
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::getCredential()
     */
    public function testExceptionGetCredentialOnInvalidCredential(): void
    {
        $this->expectException(InvalidUserCredentialException::class);
        $this->expectExceptionMessage("This credential 'Bar' for user 'Foo' is invalid");
        
        $user = new AuthenticationUser("Foo", null);
        
        $user->getCredential("Bar");
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticationUser::deleteCredential()
     */
    public function testExceptionDeleteCredentialOnInvalidCredential(): void
    {   
        $this->expectException(InvalidUserCredentialException::class);
        $this->expectExceptionMessage("This credential 'Bar' for user 'Foo' is invalid");
        
        $user = new AuthenticationUser("Foo", null);
        
        $user->deleteCredential("Bar");
    }
    
}
