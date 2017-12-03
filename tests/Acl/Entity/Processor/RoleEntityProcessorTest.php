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

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use ZoeTest\Component\Security\Mock\ResourceMock;
use ZoeTest\Component\Security\Mock\EntityMock;
use Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use ZoeTest\Component\Security\Mock\MaskCollectionMock;

/**
 * RoleEntityProcessor testcase
 * 
 * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleEntityProcessorTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessOnBlacklist(): void
    {
        $permissionsFoo = MaskCollectionMock::initMock()
                                    ->mockCount($this->once(), 5)
                                ->finalizeMock();
        $permissionsBar = MaskCollectionMock::initMock()
                                    ->mockCount($this->once(), 2)
                                ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")
                                    ->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST_BEHAVIOUR)
                                    ->mockGetPermissions_consecutive(
                                        $this->exactly(2), 
                                            [["Foo", "Bar"], $permissionsFoo],
                                            [["Moz", "Poz"], $permissionsBar])
                                ->finalizeMock();
        $entity = EntityMock::initMock("Foo")
                                    ->mockHas_consecutively($this->exactly(3), ["Foo" => true, "Bar" => true])
                                    ->mockGet_consecutively(
                                        $this->exactly(2), 
                                        [
                                            "Foo"   =>  ["Foo", "Bar"],
                                            "Bar"   =>  ["Moz", "Poz"]
                                        ])
                                ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                                    ->mockGetRoles($this->once(), ["Foo", "Bar"])
                                    ->mockDeny($this->once(), $resource, ["Moz", "Poz"])
                                ->finalizeMock();
        
        $process = new RoleEntityProcessor();
        $this->assertNull($process->processUser($user, $resource, $entity));
        $this->assertTrue($process->isProcess());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessOnBlacklistWhenUserHasARoleWithAllPermissions(): void
    {
        $permissions = MaskCollectionMock::initMock()
                                    ->mockCount($this->once(), 0)
                                ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")
                                    ->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST_BEHAVIOUR)
                                    ->mockGetPermissions($this->once(), [], $permissions)
                                ->finalizeMock();
        $entity = EntityMock::initMock("Foo")
                                    ->mockHas_consecutively($this->exactly(2), ["Foo" => false, "Bar" => true])
                                    ->mockGet($this->once(), "Bar", [])
                                ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                                    ->mockGetRoles($this->once(), ["Foo", "Bar"])
                                    ->mockDeny($this->once(), $resource, [])
                                ->finalizeMock();
        
        $process = new RoleEntityProcessor();
        $this->assertNull($process->processUser($user, $resource, $entity));
        $this->assertTrue($process->isProcess());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessOnWhitelistResource(): void
    {
        $entityValues = [
            "Foo"   =>  ["Foo", "Bar"],
            "Bar"   =>  ["Moz", "Poz"],
            "Moz"   =>  ["Loz", "Mop"]
        ];
        $entityValues = $this->getGenerator($entityValues);
        $resource = ResourceMock::initMock("Foo")
                                ->mockGetBehaviour($this->once(), ResourceInterface::WHITELIST_BEHAVIOUR)
                            ->finalizeMock();
        $entity = EntityMock::initMock("Foo")
                                ->mockGetIterator($this->once(), $entityValues)
                            ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                                ->mockGetRoles($this->once(), ["Foo", "Bar"])
                                ->mockHasRole_consecutive($this->exactly(3), ["Foo" => true, "Bar" => true, "Moz" => false])
                                ->mockGrant($this->once(), $resource, ["Foo", "Bar", "Moz", "Poz"])
                            ->finalizeMock();
        $processor = new RoleEntityProcessor();
        
        $this->assertNull($processor->processUser($user, $resource, $entity));
        $this->assertTrue($processor->isProcess());
    }
    
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessUserSkippedEntityEmpty(): void
    {
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                            ->mockGetRoles($this->once(), ["Foo", "Bar"])
                        ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")->mockGetBehaviour($this->never(), 1)->finalizeMock();
        $entity = EntityMock::initMock("Foo")->mockIsEmpty($this->once(), true)->finalizeMock();
        
        $processor = new RoleEntityProcessor();
        $this->assertNull($processor->processUser($user, $resource, $entity));
        $this->assertTrue($processor->isProcess());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::process()
     */
    public function testProcessUserSkippedUserHasNoRole(): void
    {
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                            ->mockGetRoles($this->once(), [])
                        ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")->mockGetBehaviour($this->never(), 1)->finalizeMock();
        $entity = EntityMock::initMock("Foo")->mockIsEmpty($this->never(), false)->finalizeMock();
        
        $processor = new RoleEntityProcessor();
        $this->assertNull($processor->processUser($user, $resource, $entity));
        $this->assertTrue($processor->isProcess());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Entity\Processor\RoleEntityProcessor::getName()
     */
    public function testGetName(): void
    {
        $processor = new RoleEntityProcessor();
        $this->assertSame("RoleProcessor", $processor->getName());
    }
    
}
