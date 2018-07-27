<?php
//StrictType
declare(strict_types = 1);

/*
 * Ness
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
 
namespace NessTest\Component\Authentication\User;

use NessTest\Component\Authentication\AuthenticationTestCase;
use Ness\Component\Authentication\User\AuthenticationUser;
use Ness\Component\User\UserInterface;
use Ness\Component\Authentication\Exception\UserCredentialNotFoundException;

/**
 * AuthenticationUser testcase
 * 
 * @see \Ness\Component\Authentication\User\AuthenticationUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationUserTest extends AuthenticationTestCase
{
    
    /**
     * @see \Ness\Component\Authentication\User\AuthenticationUser::initializeFromUser()
     * @see \Ness\Component\Authentication\User\AuthenticationUser::getPassword()
     */
    public function testInitializeFromUser(): void
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $user->expects($this->once())->method("getAttributes")->will($this->returnValue([AuthenticationUser::CREDENTIAL_ATTRIBUTE_IDENTIFIER => [
            "password"  =>  "Foo",
            "Bar"       =>  "Foo"
        ], "Bar" => "Foo"]));
        $user->expects($this->once())->method("getRoles")->will($this->returnValue(["Foo", "Bar"]));
        
        $authentication = AuthenticationUser::initializeFromUser($user, ["Foo" => "Bar"]);
        
        $this->assertSame("Foo", $authentication->getName());
        $this->assertSame("Foo", $authentication->getPassword());
        $this->assertSame("Bar", $authentication->getCredential("Foo"));
        $this->assertSame("Bar", $authentication->getCredential("Foo"));
        $this->assertSame(["Bar" => "Foo"], $authentication->getAttributes());
        $this->assertSame(["Foo", "Bar"], $authentication->getRoles());
    }
    
    /**
     * @see \Ness\Component\Authentication\User\AuthenticationUser::initializeFromUser()
     */
    public function testInitializeUserWhenNoCredentialAreGiven(): void
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $user->expects($this->once())->method("getAttributes")->will($this->returnValue(["Bar" => "Foo"]));
        $user->expects($this->once())->method("getRoles")->will($this->returnValue(["Foo", "Bar"]));
        
        $authentication = AuthenticationUser::initializeFromUser($user);
        
        $this->assertSame("Foo", $authentication->getName());
        $this->assertSame(["Bar" => "Foo"], $authentication->getAttributes());
        $this->assertSame(["Foo", "Bar"], $authentication->getRoles());
    }
    
                    /**_____EXCEPTIONS_____**/

    /**
     * @see \Ness\Component\Authentication\User\AuthenticationUser::getPassword()
     */
    public function testExceptionWhenNoPasswordHasBeenDefined(): void
    {
        $this->expectException(UserCredentialNotFoundException::class);
        $this->expectExceptionMessage("This credential 'password' is not setted for user 'FooUser'");
        
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("FooUser"));
        
        (AuthenticationUser::initializeFromUser($user))->getPassword();
    }
    
    /**
     * @see \Ness\Component\Authentication\User\AuthenticationUser::initializeFromUser()
     */
    public function testExceptionWhenACredentialIsNotSetted(): void
    {
        $this->expectException(UserCredentialNotFoundException::class);
        $this->expectExceptionMessage("This credential 'Foo' is not setted for user 'FooUser'");
        
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("FooUser"));
        
        (AuthenticationUser::initializeFromUser($user))->getCredential("Foo");
    }
    
    /**
     * @see \Ness\Component\Authentication\User\AuthenticationUser::initializeFromUser()
     */
    public function testExceptionWhenCredentialAttributeIsNotAnArray(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage("Credentials attribute MUST be an array with each credential indexed by its name. 'string' given");
        
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $user->expects($this->once())->method("getAttributes")->will($this->returnValue([AuthenticationUser::CREDENTIAL_ATTRIBUTE_IDENTIFIER => "Foo", "Bar" => "Foo"]));
        $user->expects($this->once())->method("getRoles")->will($this->returnValue(["Foo", "Bar"]));
        
        $authentication = AuthenticationUser::initializeFromUser($user);
    }
    
}