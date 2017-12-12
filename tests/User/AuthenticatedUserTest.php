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

namespace Zoe\Component\Security\User;

use PHPUnit\Framework\TestCase;

/**
 * AuthenticatedUser testcase
 * 
 * @see \Zoe\Component\Security\User\AuthenticatedUser
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticatedUserTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticatedUser
     */
    public function testInterface(): void
    {
        $user = new AuthenticatedUser("Foo", new \DateTime());
        
        $this->assertInstanceOf(UserInterface::class, $user);
        $this->assertInstanceOf(AuthenticatedUserInterface::class, $user);
        $this->assertInstanceOf(\JsonSerializable::class, $user);
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticatedUser::authenticatedAt()
     */
    public function testAuthenticatedAt(): void
    {
        $time = new \DateTime();
        $user = new AuthenticatedUser("Foo", $time);
        
        $this->assertSame($time, $user->authenticatedAt());
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticatedUser::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $user = new AuthenticatedUser("Foo", new \DateTime(), ["Foo" => "Bar", "Bar" => "Foo"], ["Foo", "Bar"]);
        
        $this->assertNotFalse(\json_encode($user));
    }
    
    /**
     * @see \Zoe\Component\Security\User\AuthenticatedUser::restoreFromJson()
     */
    public function testRestoreFromJson(): void
    {
        // nedeed to skip microseconds checking
        $time = \DateTime::createFromFormat("U", (string)\time());
        $user = new AuthenticatedUser("Foo", $time, ["Foo" => "Bar", "Bar" => "Foo"], ["Foo", "Bar"]);
        
        $json = \json_encode($user);
        
        // let the factory unjsonified user
        $this->assertEquals($user, AuthenticatedUser::restoreFromJson($json));
        // unjsonified before passing it to the factory
        $json = \json_decode($json, true);
        $this->assertEquals($user, AuthenticatedUser::restoreFromJson($json));
    }
    
}
