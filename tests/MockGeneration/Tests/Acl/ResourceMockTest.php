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

namespace ZoeTest\Component\Security\MockGeneration\Tests\Acl;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\Acl\InvalidEntityException;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\Acl\AclUserInterface;

/**
 * ResourceMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceMockTest extends TestCase
{
 
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetName()
     */
    public function testMockGetName(): void
    {
        $resource = ResourceMock::init("Foo")->mockGetName($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $resource->getName());
    }  
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermissions()
     */
    public function testMockGetPermissions(): void
    {
        $collection = MaskCollectionMock::init("ResourceCollection")->finalizeMock();
        $resource = ResourceMock::init("Foo")->mockGetPermissions($this->once(), null, $collection)->finalizeMock();
        
        $this->assertSame($collection, $resource->getPermissions());
        
        $resource = ResourceMock::init("Foo")->mockGetPermissions($this->once(), ["Foo", "Bar"], $collection)->finalizeMock();
        $this->assertSame($collection, $resource->getPermissions(["Foo", "Bar"]));
        
        $this->expectException(InvalidPermissionException::class);
        $resource = ResourceMock::init("Foo")->mockGetPermissions($this->once(), null, null, "Foo")->finalizeMock();
        $resource->getPermissions();
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermissions()
     */
    public function testExceptionParameterInvalidPermissionWhenMockGetPermissions(): void
    {
        $resource = ResourceMock::init("Foo")->mockGetPermissions($this->once(), null, null, "Foo")->finalizeMock();
        
        try {
            $resource->getPermissions();
        } catch (InvalidPermissionException $e) {
            $this->assertSame("Foo", $e->getInvalidPermission());
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermissions_consecutive()
     */
    public function testMockGetPermissions_consecutive(): void
    {
        $collectionFoo = MaskCollectionMock::init("ResourceCollectionFoo")->finalizeMock();
        $collectionBar = MaskCollectionMock::init("ResourceCollectionBar")->finalizeMock();
        
        $resource = ResourceMock::init("Foo")
                                    ->mockGetPermissions_consecutive(
                                        $this->exactly(2), 
                                        [
                                            [null], 
                                            [["Foo", "Bar"]]
                                        ], 
                                        null,
                                        $collectionFoo, $collectionBar)
                                ->finalizeMock();
        
        $this->assertSame($collectionFoo, $resource->getPermissions());
        $this->assertSame($collectionBar, $resource->getPermissions(["Foo", "Bar"]));
        
        $this->expectException(InvalidPermissionException::class);
        $resource = ResourceMock::init("Foo")
                                    ->mockGetPermissions_consecutive(
                                        $this->exactly(2), 
                                        [
                                            [null], 
                                            [["Foo", "Bar"]]
                                        ], 
                                        null,
                                        $collectionFoo, null)
                                ->finalizeMock();
        
        $this->assertSame($collectionFoo, $resource->getPermissions());
        $resource->getPermissions(["Foo", "Bar"]);
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermissions_consecutive()
     */
    public function testExceptionParameterInvalidPermissionWhenMockGetPermissions_consecutive(): void
    {
        $collection = MaskCollectionMock::init("Collection")->finalizeMock();
        $resource = ResourceMock::init("Foo")
                                    ->mockGetPermissions_consecutive(
                                        $this->exactly(2), 
                                        [
                                            [null],
                                            [["Foo", "Bar"]]
                                        ],
                                        "Foo",
                                        $collection, null)
                                ->finalizeMock();
        
        try {
            $resource->getPermissions();
            $resource->getPermissions(["Foo", "Bar"]);
        } catch (InvalidPermissionException $e) {
            $this->assertSame("Foo", $e->getInvalidPermission());
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermission()
     */
    public function testMockGetPermission(): void
    {
        $permission = MaskMock::init("Foo")->finalizeMock();
        $resource = ResourceMock::init("Foo")->mockGetPermission($this->once(), "Foo", $permission)->finalizeMock();
        
        $this->assertSame($permission, $resource->getPermission("Foo"));
        
        $this->expectException(InvalidPermissionException::class);
        $resource = ResourceMock::init("Foo")->mockGetPermission($this->once(), "Foo", null)->finalizeMock();
        
        $resource->getPermission("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermission()
     */
    public function testExceptionParameterInvalidPermissionWhenMockGetPermission(): void
    {
        $resource = ResourceMock::init("Foo")->mockGetPermission($this->once(), "Foo", null)->finalizeMock();
        
        try {
            $resource->getPermission("Foo");
        } catch (InvalidPermissionException $e) {
            $this->assertSame("Foo", $e->getInvalidPermission());
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermission_consecutive()
     */
    public function testMockGetPermission_consecutive(): void
    {
        $permissionFoo = MaskMock::init("Foo")->finalizeMock();
        $permissionBar = MaskMock::init("Bar")->finalizeMock();
        
        $resource = ResourceMock::init("Foo")
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        null,
                                        $permissionFoo, $permissionBar)
                                ->finalizeMock();
        
        $this->assertSame($permissionFoo, $resource->getPermission("Foo"));
        $this->assertSame($permissionBar, $resource->getPermission("Bar"));
        
        $this->expectException(InvalidPermissionException::class);
        $resource = ResourceMock::init("Foo")
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        null,
                                        $permissionFoo, null)
                                ->finalizeMock();
        
        $this->assertSame($permissionFoo, $resource->getPermission("Foo"));
        $resource->getPermission("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermission_consecutive()
     */
    public function testExceptionParameterInvalidPermissionWhenMockGetPermission_consecutive(): void
    {
        $mask = MaskMock::init("MaskPermission")->finalizeMock();
        $resource = ResourceMock::init("Foo")
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        "Bar", 
                                        $mask, null)
                                ->finalizeMock();
        
        try {
            $resource->getPermission("Foo");
            $resource->getPermission("Bar");
        } catch (InvalidPermissionException $e) {
            $this->assertSame("Bar", $e->getInvalidPermission());
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockHasPermission()
     */
    public function testMockHasPermission(): void
    {
        $resource = ResourceMock::init("Foo")->mockHasPermission($this->once(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($resource->hasPermission("Foo"));
        
        $resource = ResourceMock::init("Foo")->mockHasPermission($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($resource->hasPermission("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockHasPermission_consecutive()
     */
    public function testMockHasPermission_consecutive(): void
    {
        $resource = ResourceMock::init("Foo")
                                    ->mockHasPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        true, false)
                                ->finalizeMock();
        
        $this->assertTrue($resource->hasPermission("Foo"));
        $this->assertFalse($resource->hasPermission("Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetEntities()
     */
    public function testGetEntities(): void
    {
        $entities = [
            "Foo"   =>  EntityMock::init("EntityFooAdded")->finalizeMock(),
            "Bar"   =>  EntityMock::init("EntityBarAdded")->finalizeMock()
        ];
        
        $resource = ResourceMock::init("Foo")->mockGetEntities($this->once(), $entities)->finalizeMock();
        
        $this->assertSame($entities, $resource->getEntities());
        
        $resource = ResourceMock::init("Foo")->mockGetEntities($this->once(), null)->finalizeMock();
        
        $this->assertNull($resource->getEntities());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetEntity()
     */
    public function testGetEntity(): void
    {
        $entity = EntityMock::init("EntityGetted")->finalizeMock();
        
        $resource = ResourceMock::init("Foo")->mockGetEntity($this->once(), "Foo", $entity)->finalizeMock();
        
        $this->assertSame($entity, $resource->getEntity("Foo"));
        
        $this->expectException(InvalidEntityException::class);
        $entity = null;
        $resource = ResourceMock::init("Foo")->mockGetEntity($this->once(), "Foo", $entity)->finalizeMock();
        
        $resource->getEntity("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetEntity_consecutive()
     */
    public function testGetEntity_consecutive(): void
    {
        $entityFoo = EntityMock::init("EntityFooGetted")->finalizeMock();
        $entityBar = EntityMock::init("EntityBarGetted")->finalizeMock();
        
        $resource = ResourceMock::init("Foo")
                                    ->mockGetEntity_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        $entityFoo, $entityBar)
                                ->finalizeMock();
        
        $this->assertSame($entityFoo, $resource->getEntity("Foo"));
        $this->assertSame($entityBar, $resource->getEntity("Bar"));
        
        $this->expectException(InvalidEntityException::class);        
        $entityFoo = EntityMock::init("EntityFooGetted")->finalizeMock();
        $resource = ResourceMock::init("Foo")
                                    ->mockGetEntity_consecutive(
                                        $this->exactly(2),
                                        [["Foo"], ["Bar"]],
                                        $entityFoo, null)
                                ->finalizeMock();
                                        
        $this->assertSame($entityFoo, $resource->getEntity("Foo"));
        $resource->getEntity("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetBehaviour()
     */
    public function testMockGetBehaviour(): void
    {
        $resource = ResourceMock::init("Foo")->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST)->finalizeMock();
        
        $this->assertSame(ResourceInterface::BLACKLIST, $resource->getBehaviour());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockProcess()
     */
    public function testMockProcess(): void
    {
        $user = UserMock::init("UserProcessed", AclUserInterface::class)->finalizeMock();
        $resource = ResourceMock::init("Foo")->mockProcess($this->once(), $user, [])->finalizeMock();
        
        $this->assertNull($resource->process($user, []));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockIsProcessed()
     */
    public function testMockIsProcessed(): void
    {
        $resource = ResourceMock::init("Foo")->mockIsProcessed($this->once(), true)->finalizeMock();
        
        $this->assertTrue($resource->isProcessed());
        
        $resource = ResourceMock::init("Foo")->mockIsProcessed($this->once(), false)->finalizeMock();
        
        $this->assertFalse($resource->isProcessed());
    }
    
}
