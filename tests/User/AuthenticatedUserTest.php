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
use Ness\Component\Authentication\User\AuthenticatedUser;
use Ness\Component\User\UserInterface;

/**
 * AuthenticatedUser testcase
 * 
 * @see \Ness\Component\Authentication\User\AuthenticatedUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticatedUserTest extends AuthenticationTestCase
{
    
    /**
     * @see \Ness\Component\Authentication\User\AuthenticatedUser::authenticatedAt()
     */
    public function testAuthenticatedAt(): void
    {
        $user = AuthenticatedUser::initializeFromUser($this->getMockBuilder(UserInterface::class)->getMock());
        
        $this->assertEquals((new \DateTime())->format("d/m/Y H:i:s"), $user->authenticatedAt()->format("d/m/Y H:i:s"));
    }
    
    /**
     * @see \Ness\Component\Authentication\User\AuthenticatedUser::isRoot()
     */
    public function testIsRoot(): void
    {
        $user = AuthenticatedUser::initializeFromUser($this->getMockBuilder(UserInterface::class)->getMock());
        
        $this->assertFalse($user->isRoot());
        
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())->method("getAttributes")->will($this->returnValue([AuthenticatedUser::ROOT_ATTRIBUTE_IDENTIFIER => true]));
        
        $user = AuthenticatedUser::initializeFromUser($user);
        
        $this->assertTrue($user->isRoot());
    }
    
    /**
     * @see \Ness\Component\Authentication\User\AuthenticatedUser::initializeFromUser()
     */
    public function testInitializeFromUser(): void
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())->method("getName")->will($this->returnValue("Foo"));
        $user->expects($this->once())->method("getAttributes")->will($this->returnValue(["Foo" => "Bar"]));
        $user->expects($this->once())->method("getRoles")->will($this->returnValue(["Foo", "Bar"]));
        
        $user = AuthenticatedUser::initializeFromUser($user);
        
        $this->assertSame("Foo", $user->getName());
        $this->assertSame(["Foo" => "Bar"], $user->getAttributes());
        $this->assertSame(["Foo", "Bar"], $user->getRoles());
        $this->assertFalse($user->isRoot());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Ness\Component\Authentication\User\AuthenticatedUser::initializeFromUser()
     */
    public function testExceptionWhenRootAttributeIsNotABoolean(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage("Root attribute MUST a boolean. 'NULL' given");
        
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())->method("getAttributes")->will($this->returnValue([AuthenticatedUser::ROOT_ATTRIBUTE_IDENTIFIER => null]));
        
        AuthenticatedUser::initializeFromUser($user);
    }
    
}
