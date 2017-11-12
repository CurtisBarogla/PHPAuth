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

namespace ZoeTest\Component\Security\Collection\Strategy;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Collection\Strategy\AuthenticationStrategyCollection;

/**
 * AuthenticationStrategyCollection testcase 
 * 
 * @see \Zoe\Component\Security\Collection\Strategy\AuthenticationStrategyCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthenticationStrategyCollectionTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Collection\Strategy\AuthenticationStrategyCollection::add()
     */
    public function testAdd(): void
    {
        $strategy = $this->getMockedAuthenticateStrategy();
        
        $collection = new AuthenticationStrategyCollection();
        $this->assertNull($collection->add($strategy));
    }
    
    /**
     * @see \Zoe\Component\Security\Collection\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcess(): void
    {
        $user1 = $this->getMockedUser("foo", "bar");
        $user2 = $this->getMockedUser("bar", "foo");
        
        $strategy = $this->getMockedAuthenticateStrategy($user1, $user2, true);
        $strategy2 = $this->getMockedAuthenticateStrategy($user1, $user2, true);
        
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy);
        $collection->add($strategy2);
        
        $this->assertTrue($collection->process($user1, $user2));
    }
    
    /**
     * @see \Zoe\Component\Security\Collection\Strategy\AuthenticationStrategyCollection::process()
     */
    public function testProcessWithError(): void
    {
        $user1 = $this->getMockedUser("foo", "bar");
        $user2 = $this->getMockedUser("bar", "foo");
        
        $strategy = $this->getMockedAuthenticateStrategy($user1, $user2, false);
        $strategy2 = $this->getMockedAuthenticateStrategy($user1, $user2, true);
        
        $collection = new AuthenticationStrategyCollection();
        $collection->add($strategy);
        $collection->add($strategy2);
        
        $this->assertFalse($collection->process($user1, $user2));
    }
    
}
