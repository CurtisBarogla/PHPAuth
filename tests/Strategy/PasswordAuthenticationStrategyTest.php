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
 
namespace NessTest\Component\Authentication\Strategy;

use NessTest\Component\Authentication\AuthenticationTestCase;
use Ness\Component\Authentication\User\AuthenticationUserInterface;
use Ness\Component\Password\Hash\PasswordHashInterface;
use Ness\Component\Password\Password;
use Ness\Component\Authentication\Strategy\PasswordAuthenticationStrategy;
use Ness\Component\Authentication\Strategy\AuthenticationStrategyInterface;

/**
 * PasswordAuthenticationStrategy testcase
 * 
 * @see \Ness\Component\Authentication\Strategy\PasswordAuthenticationStrategy
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordAuthenticationStrategyTest extends AuthenticationTestCase
{
    
    /**
     * @see \Ness\Component\Authentication\Strategy\PasswordAuthenticationStrategy::process()
     */
    public function testProcess(): void
    {
        if(!\class_exists("Ness\Component\Password\Hash\PasswordHashInterface"))
            $this->markTestSkipped("PasswordHash not found");
        
        $userLoaded = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $userLoaded->expects($this->exactly(2))->method("getPassword")->will($this->returnValue("Foo"));
        
        $user = $this->getMockBuilder(AuthenticationUserInterface::class)->getMock();
        $user->expects($this->exactly(2))->method("getPassword")->will($this->onConsecutiveCalls("Foo", "Bar"));
        
        $hash = $this->getMockBuilder(PasswordHashInterface::class)->getMock();
        $hash
            ->expects($this->exactly(2))
            ->method("verify")
            ->withConsecutive([new Password("Foo"), "Foo"], [new Password("Bar"), "Foo"])
            ->will($this->onConsecutiveCalls(true, false));
        
        $strategy = new PasswordAuthenticationStrategy($hash);
        
        $this->assertNull($strategy->setLoadedUser($userLoaded));
        $this->assertSame($userLoaded, $strategy->getLoadedUser());
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $strategy->process($user));
        $this->assertSame(AuthenticationStrategyInterface::ERROR, $strategy->process($user));
    }
    
}
