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

namespace ZoeTest\Component\Security\Acl\Entity;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\EntityMock;
use ZoeTest\Component\Security\Mock\ResourceMock;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\User\Contracts\AclUserInterface;

/**
 * UsernameEntityProcessor testcase
 * 
 * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UsernameEntityProcessorTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::processUser()
     */
    public function testProcessUserBlacklist(): void
    {
        $entity = EntityMock::initMock("Foo")
                            ->mockIsEmpty($this->once(), false)
                            ->mockHas($this->once(), "Foo", true)
                            ->mockGet($this->once(), "Foo", ["Foo", "Bar"])
                        ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")
                            ->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST_BEHAVIOUR)
                        ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                            ->mockGetName($this->exactly(2))
                            ->mockDeny($this->once(), $resource, ["Foo", "Bar"])
                            ->mockGrant($this->never(), $resource, ["Foo", "Bar"])
                        ->finalizeMock();
        
        $processor = new UsernameEntityProcessor();
        
        $this->assertNull($processor->processUser($user, $resource, $entity));
        $this->assertTrue($processor->isProcess());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::processUser()
     */
    public function testProcessUserWhitelist(): void
    {
        $entity = EntityMock::initMock("Foo")
                            ->mockIsEmpty($this->once(), false)
                            ->mockHas($this->once(), "Foo", true)
                            ->mockGet($this->once(), "Foo", ["Foo", "Bar"])
                        ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")
                            ->mockGetBehaviour($this->once(), ResourceInterface::WHITELIST_BEHAVIOUR)
                        ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                            ->mockGetName($this->exactly(2))
                            ->mockDeny($this->never(), $resource, ["Foo", "Bar"])
                            ->mockGrant($this->once(), $resource, ["Foo", "Bar"])
                        ->finalizeMock();
        
        $processor = new UsernameEntityProcessor();
        
        $this->assertNull($processor->processUser($user, $resource, $entity));
        $this->assertTrue($processor->isProcess());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::processUser()
     */
    public function testProcessUserSkippedEntityEmpty(): void
    {
        $entity = EntityMock::initMock("Foo")
                            ->mockIsEmpty($this->once(), true)
                            ->mockHas($this->never(), "Foo", true)
                            ->mockGet($this->never(), "Foo", null)
                        ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")
                            ->mockGetBehaviour($this->never(), 1)
                        ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                            ->mockGetName($this->never())
                        ->finalizeMock();
        
        $processor = new UsernameEntityProcessor();
        
        $this->assertNull($processor->processUser($user, $resource, $entity));
        $this->assertTrue($processor->isProcess());
        
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::processUser()
     */
    public function testProcessUserSkippedUserNotRegisteredIntoEntity(): void
    {
        $entity = EntityMock::initMock("Foo")
                            ->mockIsEmpty($this->once(), false)
                            ->mockHas($this->once(), "Foo", false)
                            ->mockGet($this->never(), "Foo", null)
                        ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")
                            ->mockGetBehaviour($this->never(), 1)
                        ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                            ->mockGetName($this->once())
                        ->finalizeMock();
        
        $processor = new UsernameEntityProcessor();
        
        $this->assertNull($processor->processUser($user, $resource, $entity));
        $this->assertTrue($processor->isProcess());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::getName()
     */
    public function testGetName(): void
    {
        $processor = new UsernameEntityProcessor();
        
        $this->assertSame("UsernameProcessor", $processor->getName());
    }
    
}
