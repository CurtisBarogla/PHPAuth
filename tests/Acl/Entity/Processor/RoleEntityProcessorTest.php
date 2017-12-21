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
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\Acl\AclUserInterface;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock;

/**
 * RoleEntityProcessor testcase
 * 
 * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleEntityProcessorTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessWhenUserHasNoRole(): void
    {
        $user = UserMock::init("AclUser", AclUserInterface::class)->mockGetRoles($this->once(), [])->finalizeMock();
        $resource = ResourceMock::init("LinkedResource")->mockGetBehaviour($this->never(), ResourceInterface::BLACKLIST)->finalizeMock();
        $entity = EntityMock::init("EntityRoleProcessed")->mockGetResource($this->never(), $resource)->finalizeMock();
        
        $processor = new RoleEntityProcessor();
        
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessWhenEntityHasNoValue(): void
    {
        $user = UserMock::init("AclUser", AclUserInterface::class)->mockGetRoles($this->once(), [])->finalizeMock();
        $resource = ResourceMock::init("LinkedResource")->mockGetBehaviour($this->never(), ResourceInterface::BLACKLIST)->finalizeMock();
        $entity = EntityMock::init("EntityRoleProcessed")
                                ->mockIsEmpty($this->once(), true)
                                ->mockGetResource($this->never(), $resource)
                            ->finalizeMock();
        
        $processor = new RoleEntityProcessor();
        
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessOnAWhitelistBehaviourResource(): void
    {
        $resource = ResourceMock::init("LinkedResource")->mockGetBehaviour($this->once(), ResourceInterface::WHITELIST)->finalizeMock();
        $user = UserMock::init("AclUser", AclUserInterface::class)
                            ->mockGrant($this->once(), $resource, [0 => "FooPerm", 1 => "BarPerm", 3 => "MozPerm"], false)
                            ->mockGetRoles($this->once(), ["Foo", "Bar", "Moz"])
                        ->finalizeMock();
        $entity = EntityMock::init("EntityRoleProcessed")
                                ->mockGetResource($this->once(), $resource)
                                ->mockHas_consecutive(
                                    $this->exactly(3), 
                                    [["Foo"], ["Bar"], ["Moz"]], 
                                    false, true, true)
                                ->mockGet_consecutive(
                                    $this->exactly(2), 
                                    [["Bar"], ["Moz"]], 
                                    null, 
                                    ["FooPerm", "BarPerm"], ["FooPerm", "MozPerm"])
                            ->finalizeMock();
        
        $processor = new RoleEntityProcessor();
        
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessOnABlacklistResourceWithARoleWithNoPermissionToDeny(): void
    {
        $resource = ResourceMock::init("LinkedResource")->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST)->finalizeMock();
        $user = UserMock::init("AclUser", AclUserInterface::class)
                            ->mockDeny($this->once(), $resource, [], false)
                            ->mockGetRoles($this->once(), ["Foo", "Bar", "Moz"])
                        ->finalizeMock();
        $entity = EntityMock::init("EntityRoleProcessed")
                            ->mockGetResource($this->once(), $resource)
                            ->mockHas_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                true, true)
                            ->mockGet_consecutive(
                                $this->exactly(2), 
                                [["Foo"], ["Bar"]], 
                                null, 
                                ["FooPerm", "BarPerm"], [])
                        ->finalizeMock();
        
        $processor = new RoleEntityProcessor();
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessOnABlacklistResourceWithoutEquivoque(): void
    {
        $resource = ResourceMock::init("LinkedResource")->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST)->finalizeMock();
        $user = UserMock::init("AclUser", AclUserInterface::class)
                            ->mockDeny($this->once(), $resource, ["FooPerm", "BarPerm"], false)
                            ->mockGetRoles($this->once(), ["Foo", "Bar", "Moz"])
                        ->finalizeMock();
        $entity = EntityMock::init("EntityRoleProcessed")
                                ->mockGetResource($this->once(), $resource)
                                ->mockHas_consecutive(
                                    $this->exactly(3), 
                                    [["Foo"], ["Bar"], ["Moz"]], 
                                    false, true, true)
                                ->mockGet_consecutive(
                                    $this->exactly(2), 
                                    [["Bar"], ["Moz"]], 
                                    null, 
                                    ["FooPerm", "BarPerm", "MozPerm"], ["FooPerm", "BarPerm"])
                            ->finalizeMock();
                            
        $processor = new RoleEntityProcessor();
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessOnABlacklistResourceWithEquivoque(): void
    {
        $mask1 = MaskMock::init("MaskPermFooBar")->mockGetValue($this->once(), 8)->finalizeMock();
        $mask2 = MaskMock::init("MaskPermMozPoz")->mockGetValue($this->once(), 1)->finalizeMock();
        $collection1 = MaskCollectionMock::init("CollectionFooBar")->mockTotal($this->once(), null, null, $mask1)->finalizeMock();
        $collection2 = MaskCollectionMock::init("CollectionMozPoz")->mockTotal($this->once(), null, null, $mask2)->finalizeMock();
        $resource = ResourceMock::init("LinkedResource")
                                    ->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST)
                                    ->mockGetPermissions_consecutive(
                                        $this->exactly(2), 
                                        [
                                            [["FooPerm", "BarPerm"]],
                                            [["MozPerm", "PozPerm"]]
                                        ], 
                                        null, 
                                        $collection1, $collection2)
                                ->finalizeMock();
        $entity = EntityMock::init("EntityRoleProcessed")
                                ->mockGetResource($this->once(), $resource)
                                ->mockHas_consecutive(
                                    $this->exactly(2), 
                                    [["Foo"], ["Bar"]], 
                                    true, true)
                                ->mockGet_consecutive(
                                    $this->exactly(2), 
                                    [["Foo"], ["Bar"]], 
                                    null, 
                                    ["FooPerm", "BarPerm"], ["MozPerm", "PozPerm"])
                            ->finalizeMock();
        $user = UserMock::init("AclUser", AclUserInterface::class)
                            ->mockGetRoles($this->once(), ["Foo", "Bar"])
                            ->mockDeny($this->once(), $resource, ["MozPerm", "PozPerm"], false)
                        ->finalizeMock();
        
        $processor = new RoleEntityProcessor();
        $this->assertNull($processor->process($entity, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::getIdentifier()
     */
    public function testGetIdentifier(): void
    {
        $processor = new RoleEntityProcessor();
        
        $this->assertSame("RoleProcessor", $processor->getIdentifier());
    }
    
}
