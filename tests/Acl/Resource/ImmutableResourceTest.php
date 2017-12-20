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

namespace ZoeTest\Component\Security\Acl\Resource;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;
use Zoe\Component\Security\Acl\Resource\ImmutableResource;
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use Zoe\Component\Security\Acl\Entity\EntityInterface;
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Acl\Resource\Resource;

/**
 * ImmutableResource testcase
 * 
 * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ImmutableResourceTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::getName()
     */
    public function testGetName(): void
    {
        $mutable = $this->getMockedResource();
        $immutable = new ImmutableResource($mutable);
        
        $this->assertSame("Foo", $immutable->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::addPermission()
     */
    public function testAddPermission(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $mutable = $this->getMockedResource();
        $immutable = new ImmutableResource($mutable);
        
        $immutable->addPermission("Foo");
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::getPermissions()
     */
    public function testGetPermissions(): void    
    {
        $mutable = $this->getMockedResource();
        $immutable = new ImmutableResource($mutable);
        
        $this->assertTrue($immutable->getPermissions(["Foo", "Bar"])->has("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::getPermission()
     */
    public function testGetPermission(): void
    {
        $mutable = $this->getMockedResource();
        $immutable = new ImmutableResource($mutable);
        
        $this->assertSame("Foo", $immutable->getPermission("Foo")->getIdentifier());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::hasPermission()
     */
    public function testHasPermission(): void
    {
        $mutable = $this->getMockedResource();
        $immutable = new ImmutableResource($mutable);
        
        $this->assertTrue($immutable->hasPermission("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::addEntity()
     */
    public function testAddEntity(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("Cannot add entity on resource 'Foo' as it is set to an immutable state");
        
        $entity = EntityMock::init("EntityAddOnImmutableResource")->finalizeMock();
        $immutable = new ImmutableResource($this->getMockedResource());
        
        $immutable->addEntity($entity);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::getEntities()
     */
    public function testGetEntities(): void
    {
        $immutable = new ImmutableResource($this->getMockedResource());
        
        foreach ($immutable->getEntities() as $entity) {
            $this->assertInstanceOf(EntityInterface::class, $entity);
        }
        
        $resource = ResourceMock::init("ResourceForNullEntitiesWrapped", ResourceInterface::class)->mockGetEntities($this->once(), null)->finalizeMock();
        
        $immutable = new ImmutableResource($resource);
        
        $this->assertNull($immutable->getEntities());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::getEntity()
     */
    public function testGetEntity(): void
    {
        $immutable = new ImmutableResource($this->getMockedResource());
        
        $this->assertSame("Foo", $immutable->getEntity("Foo")->getIdentifier());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::getBehaviour()
     */
    public function testGetBehaviour(): void
    {
        $mutable = $this->getMockedResource();
        $immutable = new ImmutableResource($mutable);
        
        $this->assertSame(ResourceInterface::BLACKLIST, $immutable->getBehaviour());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $entity = new Entity("Foo");
        $entity->add("Foo", ["Foo", "Bar"]);
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->addPermission("Foo");
        $resource->addEntity($entity);
        $immutable = new ImmutableResource($resource);
        
        $this->assertNotFalse(\json_encode($immutable));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::restoreFromJson()
     */
    public function testRestoreFromJson(): void
    {
        $entity = new Entity("Foo");
        $entity->add("Foo", ["Foo", "Bar"]);
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->addPermission("Foo");
        $resource->addEntity($entity);
        $immutable = new ImmutableResource($resource);
        
        $json = \json_encode($immutable);
        
        $this->assertEquals($immutable, ImmutableResource::restoreFromJson($json));
        
        $json = \json_decode($json, true);
        
        $this->assertEquals($immutable, ImmutableResource::restoreFromJson($json));
    }
    
    /**
     * Get a fully mocked Resource for wrapper testing
     * 
     * @return ResourceInterface
     *   Resource mocked
     */
    private function getMockedResource(): ResourceInterface
    {
        $permissions = MaskCollectionMock::init("PermissionsFromWrappedToImmutableResource")
                                            ->mockHas($this->once(), "Foo", true)
                                       ->finalizeMock();
        
        $permission = MaskMock::init("PermissionFromWrappedToImmutableResource")
                                ->mockGetIdentifier($this->once(), "Foo")            
                            ->finalizeMock();
        
        $entityFoo = EntityMock::init("EntityFoo")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        $entityBar = EntityMock::init("EntityBar")->finalizeMock();
        
        return ResourceMock::init("ResourceWrappedToimmutable", ResourceInterface::class)
                                ->mockGetName($this->once(), "Foo")
                                ->mockAddPermission($this->never(), "Foo")
                                ->mockGetPermissions($this->once(), ["Foo", "Bar"], $permissions)
                                ->mockGetPermission($this->once(), "Foo", $permission)
                                ->mockHasPermission($this->once(), "Foo", true)
                                ->mockGetEntities($this->exactly(1), ["Foo" => $entityFoo, "Bar" => $entityBar])
                                ->mockGetEntity($this->exactly(1), "Foo", $entityFoo)
                                ->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST)
                            ->finalizeMock();     
    }
    
}
