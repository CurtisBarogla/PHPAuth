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
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Resource\ImmutableResourceInterface;

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
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockGetName($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $resource->getName());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockAddPermission()
     */
    public function testMockAddPermission(): void
    {
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockAddPermission($this->once(), "Foo")->finalizeMock();
        
        $this->assertNull($resource->addPermission("Foo"));
        
        $this->expectException(\BadMethodCallException::class);
        
        $resource = ResourceMock::init("Foo", ImmutableResourceInterface::class)->mockAddPermission($this->once(), "Foo")->finalizeMock();
        $resource->addPermission("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockAddPermission_consecutive()
     */
    public function testMockAddPermission_consecutive(): void
    {
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockAddPermission_consecutive($this->exactly(2), [["Foo"], ["Bar"]])->finalizeMock();
        
        $this->assertNull($resource->addPermission("Foo"));
        $this->assertNull($resource->addPermission("Bar"));
        
        $this->expectException(\BadMethodCallException::class);
        $resource = ResourceMock::init("Foo", ImmutableResourceInterface::class)->mockAddPermission_consecutive($this->exactly(1), [["Foo"], ["Bar"]])->finalizeMock();
        $resource->addPermission("Foo");
    }   
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermissions()
     */
    public function testMockGetPermissions(): void
    {
        $collection = MaskCollectionMock::init("ResourceCollection")->finalizeMock();
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockGetPermissions($this->once(), null, $collection)->finalizeMock();
        
        $this->assertSame($collection, $resource->getPermissions());
        
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockGetPermissions($this->once(), ["Foo", "Bar"], $collection)->finalizeMock();
        $this->assertSame($collection, $resource->getPermissions(["Foo", "Bar"]));
        
        $this->expectException(InvalidPermissionException::class);
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockGetPermissions($this->once(), null, null)->finalizeMock();
        $resource->getPermissions();
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermissions_consecutive()
     */
    public function testMockGetPermissions_consecutive(): void
    {
        $collectionFoo = MaskCollectionMock::init("ResourceCollectionFoo")->finalizeMock();
        $collectionBar = MaskCollectionMock::init("ResourceCollectionBar")->finalizeMock();
        
        $resource = ResourceMock::init("Foo", ResourceInterface::class)
                                    ->mockGetPermissions_consecutive(
                                        $this->exactly(2), 
                                        [
                                            [null], 
                                            [["Foo", "Bar"]]
                                        ], 
                                        $collectionFoo, $collectionBar)
                                ->finalizeMock();
        
        $this->assertSame($collectionFoo, $resource->getPermissions());
        $this->assertSame($collectionBar, $resource->getPermissions(["Foo", "Bar"]));
        
        $this->expectException(InvalidPermissionException::class);
        $resource = ResourceMock::init("Foo", ResourceInterface::class)
                                    ->mockGetPermissions_consecutive(
                                        $this->exactly(2), 
                                        [
                                            [null], 
                                            [["Foo", "Bar"]]
                                        ], 
                                        $collectionFoo, null)
                                ->finalizeMock();
        
        $this->assertSame($collectionFoo, $resource->getPermissions());
        $resource->getPermissions(["Foo", "Bar"]);
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermission()
     */
    public function testMockGetPermission(): void
    {
        $permission = MaskMock::init("Foo")->finalizeMock();
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockGetPermission($this->once(), "Foo", $permission)->finalizeMock();
        
        $this->assertSame($permission, $resource->getPermission("Foo"));
        
        $this->expectException(InvalidPermissionException::class);
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockGetPermission($this->once(), "Foo", null)->finalizeMock();
        
        $resource->getPermission("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetPermission_consecutive()
     */
    public function testMockGetPermission_consecutive(): void
    {
        $permissionFoo = MaskMock::init("Foo")->finalizeMock();
        $permissionBar = MaskMock::init("Bar")->finalizeMock();
        
        $resource = ResourceMock::init("Foo", ResourceInterface::class)
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        $permissionFoo, $permissionBar)
                                ->finalizeMock();
        
        $this->assertSame($permissionFoo, $resource->getPermission("Foo"));
        $this->assertSame($permissionBar, $resource->getPermission("Bar"));
        
        $this->expectException(InvalidPermissionException::class);
        $resource = ResourceMock::init("Foo", ResourceInterface::class)
                                    ->mockGetPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        $permissionFoo, null)
                                ->finalizeMock();
        
        $this->assertSame($permissionFoo, $resource->getPermission("Foo"));
        $resource->getPermission("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockHasPermission()
     */
    public function testMockHasPermission(): void
    {
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockHasPermission($this->once(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($resource->hasPermission("Foo"));
        
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockHasPermission($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($resource->hasPermission("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockHasPermission_consecutive()
     */
    public function testMockHasPermission_consecutive(): void
    {
        $resource = ResourceMock::init("Foo", ResourceInterface::class)
                                    ->mockHasPermission_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        true, false)
                                ->finalizeMock();
        
        $this->assertTrue($resource->hasPermission("Foo"));
        $this->assertFalse($resource->hasPermission("Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::mockGetBehaviour()
     */
    public function testMockGetBehaviour(): void
    {
        $resource = ResourceMock::init("Foo", ResourceInterface::class)->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST)->finalizeMock();
        
        $this->assertSame(ResourceInterface::BLACKLIST, $resource->getBehaviour());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock::init()
     */
    public function testExceptionOnInvalidResourceType(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Given resource type 'Foo' is invalid. Use : 'Zoe\Component\Security\Acl\Resource\ResourceInterface | Zoe\Component\Security\Acl\Resource\ImmutableResourceInterface'");
        
        $resource = ResourceMock::init("Foo", "Foo");
    }
    
}
