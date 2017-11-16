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

namespace ZoeTest\Component\Security\Authentication\Strategy;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy;
use Zoe\Component\Security\Encoder\PasswordEncoderInterface;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;

/**
 * UsernamePasswordStrategy testcase
 * 
 * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UsernamePasswordStrategyTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::process()
     */
    public function testProcess(): void
    {
        $user = $this->getMockedUser(CredentialUserInterface::class, "foo");
        $user->method("getPassword")->will($this->returnValue("foo"));
        
        $encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->setMethods(["encode", "compare"])->getMock();
        $encoder->expects($this->once())->method("compare")->with($user->getPassword(), $user->getPassword())->will($this->returnValue(true));
        
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $strategy->process($user, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::process()
     */
    public function testProcessOnFail(): void
    {
        $user = $this->getMockedUser(CredentialUserInterface::class, "foo");
        $user->method("getPassword")->will($this->returnValue("foo"));
        $user2 = $this->getMockedUser(CredentialUserInterface::class, "foo");
        $user2->method("getPassword")->will($this->returnValue("bar"));
        
        $encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->setMethods(["encode", "compare"])->getMock();
        $encoder->expects($this->once())->method("compare")->with($user->getPassword(), $user2->getPassword())->will($this->returnValue(false));
        
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $strategy->process($user2, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::process()
     */
    public function testProcessOnNonCredentialUser(): void
    {
        $user = $this->getMockedUser(MutableUserInterface::class, "foo");
        $user2 = $this->getMockedUser(CredentialUserInterface::class, "foo");
        $user2->method("getPassword")->will($this->returnValue("bar"));
        $encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->setMethods(["encode", "compare"])->getMock();
        
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user, $user2));
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user2, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::process()
     */
    public function testProcessWhenPasswordIsNull(): void
    {
        $user = $this->getMockedUser(CredentialUserInterface::class, "foo");
        $user->method("getPassword")->will($this->returnValue(null));
        $user2 = $this->getMockedUser(CredentialUserInterface::class, "foo");
        $user2->method("getPassword")->will($this->returnValue("bar"));
        $encoder = $this->getMockBuilder(PasswordEncoderInterface::class)->setMethods(["encode", "compare"])->getMock();
        
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user, $user2));
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user2, $user));
    }
    
}
