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
use ZoeTest\Component\Security\Mock\PasswordEncoderMock;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy;
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
        $encoder = PasswordEncoderMock::initMock()->mockCompare($this->once(), "Foo", "Foo", true)->finalizeMock();
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetPassword($this->exactly(2), "Foo")->finalizeMock();
        $user2 = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetPassword($this->exactly(2), "Foo")->finalizeMock();
        
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertSame(AuthenticationStrategyInterface::SUCCESS, $strategy->process($user2, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::process()
     */
    public function testProcessOnFail(): void
    {
        $encoder = PasswordEncoderMock::initMock()->mockCompare($this->once(), "Foo", "Bar", false)->finalizeMock();
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetPassword($this->exactly(2), "Foo")->finalizeMock();
        $user2 = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetPassword($this->exactly(2), "Bar")->finalizeMock();
   
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertSame(AuthenticationStrategyInterface::FAIL, $strategy->process($user2, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::process()
     */
    public function testProcessOnNonCredentialUser(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetPassword($this->exactly(2), "Foo")->finalizeMock();
        $encoder = PasswordEncoderMock::initMock()->mockCompare($this->never(), "Foo", "Foo", false)->finalizeMock();
        
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user, $user2));
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user2, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::process()
     */
    public function testProcessWhenPasswordIsNull(): void
    {
        $user = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetPassword($this->exactly(2), null)->finalizeMock();
        $user2 = UserMock::initMock(CredentialUserInterface::class, "Foo")->mockGetPassword($this->exactly(2), "Foo")->finalizeMock();
        $encoder = PasswordEncoderMock::initMock()->mockCompare($this->never(), "Foo", "Foo", false)->finalizeMock();
        
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user, $user2));
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user2, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\UsernamePasswordStrategy::handle()
     */
    public function testHandle(): void
    {
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $encoder = PasswordEncoderMock::initMock()->finalizeMock();
        
        $strategy = new UsernamePasswordStrategy($encoder);
        
        $this->assertNull($strategy->handle($user));
    }
    
}
