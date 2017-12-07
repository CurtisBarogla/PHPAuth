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
use ZoeTest\Component\Security\Mock\RoleCollectionMock;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\Authentication\Strategy\RoleAttributionStrategy;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;

/**
 * RoleAttributionStrategy testcase
 * 
 * @see \Zoe\Component\Security\Authentication\Strategy\RoleAttributionStrategy
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleAttributionStrategyTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\RoleAttributionStrategy::process()
     */
    public function testProcess(): void
    {
        $collection = RoleCollectionMock::initMock()->finalizeMock();
        $user1 = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        $user2 = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        
        $strategy = new RoleAttributionStrategy($collection, true);
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($user1, $user2));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\Strategy\RoleAttributionStrategy::handle()
     */
    public function testHandle(): void
    {
        $roles = [
            "Foo"   =>  ["Foo", "Bar"],
            "Bar"   =>  ["Moz", "Poz"]
        ];
        $collection = RoleCollectionMock::initMock()->mockGet_consecutive($this->exactly(2), $roles)->finalizeMock();
        $user = UserMock::initMock(MutableUserInterface::class, "Foo")
                            ->mockGetRoles($this->any(), ["Foo", "Bar"])
                            ->mockAddRole_consecutive($this->exactly(4), "Moz", "Poz", "Foo", "Bar")
                        ->finalizeMock();
        
        $strategy = new RoleAttributionStrategy($collection);
        
        $userH = $strategy->handle($user);

        $this->assertInstanceOf(MutableUserInterface::class, $userH);
    }
    
}
