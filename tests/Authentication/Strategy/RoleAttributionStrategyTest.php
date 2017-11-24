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
use Zoe\Component\Security\Authentication\Strategy\RoleAttributionStrategy;
use Zoe\Component\Security\Role\RoleCollection;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;

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
        $collection = $this->getMockBuilder(RoleCollection::class)->setMethods(["getRole"])->disableOriginalConstructor()->getMock();
        $collection
            ->expects($this->exactly(2))
            ->method("getRole")
            ->withConsecutive(["Foo"], ["Moz"])
            ->willReturnOnConsecutiveCalls(["Foo"], ["Foo", "Bar", "Moz"]);
        $reflection = new \ReflectionClass(MutableUserInterface::class);
        $users = [
            $this->getMockBuilder(MutableUserInterface::class)->setMethods($this->reflection_extractMethods($reflection))->getMock(),
            $this->getMockBuilder(MutableUserInterface::class)->setMethods($this->reflection_extractMethods($reflection))->getMock()
        ];
        $users[0]
            ->expects($this->once())
            ->method("getRoles")
            ->will($this->returnValue(["Foo", "Moz"]));
        $users[0]
            ->expects($this->exactly(3))
            ->method("addRole")
            ->withConsecutive(["Foo"], ["Bar"], ["Moz"]);
        $users[1]
            ->expects($this->never())
            ->method("addRole");

        $strategy = new RoleAttributionStrategy($collection, false);
        
        $this->assertSame(AuthenticationStrategyInterface::SKIP, $strategy->process($users[0], $users[1]));
    }
    
}
