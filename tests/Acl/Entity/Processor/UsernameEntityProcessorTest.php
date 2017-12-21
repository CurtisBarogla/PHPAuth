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

namespace ZoeTest\Component\Security\Acl\Entity\Processor;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\Acl\AclUserInterface;
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * UsernameEntityProcessor testcase
 * 
 * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UsernameEntityProcessorTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::process()
     */
    public function testProcessWhenUsernameIsNotRegistered(): void
    {
        $user = UserMock::init("BarUser", AclUserInterface::class)->mockGetName($this->once(), "Bar")->finalizeMock();
        $resource = ResourceMock::init("LinkedResource")->mockGetBehaviour($this->never(), ResourceInterface::BLACKLIST)->finalizeMock();
        $entity = EntityMock::init("EntityUsernameProcessed")
                                ->mockHas($this->once(), "Bar", false)
                            ->finalizeMock();
        
        $processor = new UsernameEntityProcessor();
        
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::process()
     */
    public function testProcessWhenEntityIsEmpty(): void
    {
        $user = UserMock::init("BarUser", AclUserInterface::class)->mockGetName($this->never(), "Bar")->finalizeMock();
        $entity = EntityMock::init("EntityUsernameProcessed")
                                ->mockIsEmpty($this->once(), true)
                            ->finalizeMock();
        
        $processor = new UsernameEntityProcessor();
        
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::process()
     */
    public function testProcessOnAWhitelistBehaviourResource(): void
    {
        $resource = ResourceMock::init("LinkedResource")->mockGetBehaviour($this->once(), ResourceInterface::WHITELIST)->finalizeMock();
        $entity = EntityMock::init("EntityUsernameProcessed")
                                ->mockHas($this->once(), "Foo", true)
                                ->mockGet($this->once(), "Foo", ["FooPerm", "BarPerm"])
                                ->mockGetResource($this->once(), $resource)
                            ->finalizeMock();
        $user = UserMock::init("AclUser", AclUserInterface::class)
                            ->mockGetName($this->exactly(2), "Foo")
                            ->mockGrant($this->once(), $resource, ["FooPerm", "BarPerm"], false)
                        ->finalizeMock();
        
        $processor = new UsernameEntityProcessor();
        
        $this->assertNull($processor->process($entity, $user));
                    
                                
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::process()
     */
    public function testProcessOnABlacklistBehaviourResource(): void
    {
        $resource = ResourceMock::init("LinkedResource")->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST)->finalizeMock();
        $entity = EntityMock::init("EntityUsernameProcessed")
                                ->mockHas($this->once(), "Foo", true)
                                ->mockGet($this->once(), "Foo", ["FooPerm", "BarPerm"])
                                ->mockGetResource($this->once(), $resource)
                            ->finalizeMock();
        $user = UserMock::init("AclUser", AclUserInterface::class)
                            ->mockGetName($this->exactly(2), "Foo")
                            ->mockDeny($this->once(), $resource, ["FooPerm", "BarPerm"], false)
                        ->finalizeMock();
        
        $processor = new UsernameEntityProcessor();
        
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\UsernameEntityProcessor::getIdentifier()
     */
    public function testGetIdentifier(): void
    {
        $processor = new UsernameEntityProcessor();
        
        $this->assertSame("UsernameProcessor", $processor->getIdentifier());
    }
    
}
