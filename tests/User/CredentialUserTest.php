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

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\User\CredentialUser;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\Exception\InvalidUserCredentialException;

/**
 * CredentialUser testcase
 * 
 * @see \Zoe\Component\Security\User\CredentialUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class CredentialUserTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\CredentialUser
     */
    public function testInterface(): void
    {
        $user = new CredentialUser("foo", null);
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(MutableUserInterface::class, $user);
        $this->assertInstanceOf(CredentialUserInterface::class, $user);
    }
    
    /**
     * @see \Zoe\Component\Security\User\CredentialUser::getPassword()
     */
    public function testGetPassword(): void
    {
        $user = new CredentialUser("foo", null);
        
        $this->assertNull($user->getPassword());
       
        $user->addCredential("password", "foo");
        
        $this->assertSame("foo", $user->getPassword());
        
        $user = new CredentialUser("foo", "bar");
        
        $this->assertSame("bar", $user->getPassword());
    }
    
    /**
     * @see \Zoe\Component\Security\User\CredentialUser::getCredentials()
     */
    public function testGetCredentials(): void
    {
        $user = new CredentialUser("foo", null);
        
        $this->assertNull($user->getCredentials());
        
        $user->addCredential("foo", "bar")->addCredential("bar", "foo");
        
        $this->assertSame(["foo" => "bar", "bar" => "foo"], $user->getCredentials());
    }
    
    /**
     * @see \Zoe\Component\Security\User\CredentialUser::getCredential()
     */
    public function testGetCredential(): void
    {
        $user = new CredentialUser("foo", null);
        
        $user->addCredential("foo", "bar");
        
        $this->assertSame("bar", $user->getCredential("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\CredentialUser::hasCredential()
     */
    public function testHasCredential(): void
    {
        $user = new CredentialUser("foo", null);
        
        $this->assertFalse($user->hasCredential("foo"));
        
        $user->addCredential("foo", "bar");
        
        $this->assertTrue($user->hasCredential("foo"));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\CredentialUser::getCredential()
     */
    public function testExceptionWhenGettingAnInvalidCredential(): void
    {
        $this->expectException(InvalidUserCredentialException::class);
        $this->expectExceptionMessage("This credential 'foo' for the user 'bar' is not setted");
        
        $user = new CredentialUser("bar", null);
        
        $user->getCredential("foo");
    }
    
}
