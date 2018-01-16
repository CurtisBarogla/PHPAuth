<?php
//StrictType
declare(strict_types = 1);

/*
 * Zoe
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace ZoeTest\Component\Authentication\Strategy;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Password\Hash\PasswordHashInterface;
use Zoe\Component\Authentication\Strategy\PasswordAuthenticationStrategy;
use Zoe\Component\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\User\AuthenticationUserInterface;

/**
 * PasswordAuthenticationStrategy testcase
 * 
 * @see \Zoe\Component\Authentication\Strategy\PasswordAuthenticationStrategy
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordAuthenticationStrategyTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Authentication\Strategy\PasswordAuthenticationStrategy::process()
     */
    public function testProcess(): void
    {
        $hash = $this->getMockBuilder(PasswordHashInterface::class)->getMock();
        $hash->expects($this->once())->method("isValid")->with("FooUserGiven", "FooUserLoaded")->will($this->returnValue(true));
        $loadedUser = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loadedUser->expects($this->exactly(2))->method("getPassword")->will($this->returnValue("FooUserLoaded"));
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $user->expects($this->exactly(2))->method("getPassword")->will($this->returnValue("FooUserGiven"));
        
        $strategy = new PasswordAuthenticationStrategy($hash);
        $strategy->setUser($loadedUser);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $strategy->process($user));
    }
    
    /**
     * @see \Zoe\Component\Authentication\Strategy\PasswordAuthenticationStrategy::process()
     */
    public function testProcessError(): void
    {
        $hash = $this->getMockBuilder(PasswordHashInterface::class)->getMock();
        $hash->expects($this->once())->method("isValid")->with("FooUserGiven", "FooUserLoaded")->will($this->returnValue(false));
        $loadedUser = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loadedUser->expects($this->exactly(2))->method("getPassword")->will($this->returnValue("FooUserLoaded"));
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $user->expects($this->exactly(2))->method("getPassword")->will($this->returnValue("FooUserGiven"));
        
        $strategy = new PasswordAuthenticationStrategy($hash);
        $strategy->setUser($loadedUser);
        
        $this->assertSame(AuthenticationStrategyInterface::ERROR, $strategy->process($user));
    }
    
    /**
     * @see \Zoe\Component\Authentication\Strategy\PasswordAuthenticationStrategy::process()
     */
    public function testProcessSkip(): void
    {
        $hash = $this->getMockBuilder(PasswordHashInterface::class)->getMock();
        $hash->expects($this->never())->method("isValid");
        
        // loaded user password null
        
        $loadedUser = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loadedUser->expects($this->once())->method("getPassword")->will($this->returnValue(null));
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $user->expects($this->never())->method("getPassword")->will($this->returnValue("Foo"));
        
        $strategy = new PasswordAuthenticationStrategy($hash);
        $strategy->setUser($loadedUser);
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user));
        
        // user given password null
        
        $loadedUser = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $loadedUser->expects($this->once())->method("getPassword")->will($this->returnValue("Foo"));
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $user->expects($this->once())->method("getPassword")->will($this->returnValue(null));
        
        $strategy->setUser($loadedUser);
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user));
    }
    
}
