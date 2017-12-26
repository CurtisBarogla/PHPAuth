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

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\Password\PasswordHashingMock;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\Authentication\Strategy\PasswordAuthenticationStrategy;
use Zoe\Component\Security\User\AuthenticationUserInterface;

/**
 * PasswordAuthenticationStrategy testcase
 * 
 * @see \Zoe\Component\Security\Authentication\Strategy\PasswordAuthenticationStrategy
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordAuthenticationStrategyTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\PasswordAuthenticationStrategy::process()
     */
    public function testProcess(): void
    {
        $hashing = PasswordHashingMock::init("AuthenticationStrategyLinked")
                                        ->mockCheck($this->once(), "Foo", "Foo", null, true)
                                    ->finalizeMock();
        $user = UserMock::init("UserToProcess", AuthenticationUserInterface::class)
                            ->mockGetPassword($this->exactly(2), "Foo")
                        ->finalizeMock();
        $userLoaded = UserMock::init("UserToProcessLoaded", AuthenticationUserInterface::class)
                                ->mockGetPassword($this->exactly(2), "Foo")
                            ->finalizeMock();
        
        $strategy = new PasswordAuthenticationStrategy($hashing);
        $strategy->setLoadedUser($userLoaded);
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $strategy->process($user));
        
        // error
        $hashing = PasswordHashingMock::init("AuthenticationStrategyLinked")
                                        ->mockCheck($this->once(), "Bar", "Foo", null, false)
                                    ->finalizeMock();
        $user = UserMock::init("UserToProcess", AuthenticationUserInterface::class)
                            ->mockGetPassword($this->exactly(2), "Bar")
                        ->finalizeMock();
        $userLoaded = UserMock::init("UserToProcessLoaded", AuthenticationUserInterface::class)
                                ->mockGetPassword($this->exactly(2), "Foo")
                            ->finalizeMock();
        
        $strategy = new PasswordAuthenticationStrategy($hashing);
        $strategy->setLoadedUser($userLoaded);
        $this->assertSame(AuthenticationStrategyInterface::ERROR, $strategy->process($user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\PasswordAuthenticationStrategy::process()
     */
    public function testProcessOnSkip(): void
    {
        // given user password null
        $hashing = PasswordHashingMock::init("AuthenticationStrategyLinked")
                                        ->mockCheck($this->never(), "Bar", "Foo", null, false)
                                    ->finalizeMock();
        $user = UserMock::init("UserToProcess", AuthenticationUserInterface::class)->mockGetPassword($this->once(), null)->finalizeMock();
        $userLoaded = UserMock::init("UserToProcessLoaded", AuthenticationUserInterface::class)
                                ->mockGetPassword($this->never(), "Foo")
                            ->finalizeMock();
        
        $strategy = new PasswordAuthenticationStrategy($hashing);
        $strategy->setLoadedUser($userLoaded);
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user));
        
        // loaded user password null
        $hashing = PasswordHashingMock::init("AuthenticationStrategyLinked")
                                        ->mockCheck($this->never(), "Bar", "Foo", null, false)
                                    ->finalizeMock();
        $user = UserMock::init("UserToProcess", AuthenticationUserInterface::class)->mockGetPassword($this->once(), null)->finalizeMock();
        $userLoaded = UserMock::init("UserToProcessLoaded", AuthenticationUserInterface::class)
                                ->mockGetPassword($this->once(), null)
                            ->finalizeMock();
        
        $strategy = new PasswordAuthenticationStrategy($hashing);
        $strategy->setLoadedUser($userLoaded);
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user));
    }
    
}
