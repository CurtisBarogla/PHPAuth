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

namespace ZoeTest\Component\Security\Mock\Tests;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\MaskCollectionMock;
use ZoeTest\Component\Security\Mock\ResourceMock;
use ZoeTest\Component\Security\Mock\MaskMock;
use Zoe\Component\Security\Exception\InvalidResourcePermissionException;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use ZoeTest\Component\Security\Mock\EntityMock;
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Exception\InvalidEntityException;

/**
 * ResourceMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\ResourceMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResouceMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockAddPermission()
     */
    public function testMockAddPermission(): void
    {
        $resource = ResourceMock::initMock("Foo")->mockAddPermission($this->once(), "Foo")->finalizeMock();
        
        $this->assertNull($resource->addPermission("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockAddPermission_consecutive()
     */
    public function testMockAddPermission_consecutive(): void
    {
        $resource = ResourceMock::initMock("Foo")->mockAddPermission_consecutive($this->exactly(3), "Foo", "Bar", "Moz")->finalizeMock();
        
        $this->assertNull($resource->addPermission("Foo"));
        $this->assertNull($resource->addPermission("Bar"));
        $this->assertNull($resource->addPermission("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetPermissions()
     */
    public function testMockGetPermissions(): void
    {
        $permissions = MaskCollectionMock::initMock()->finalizeMock();
        $resource = ResourceMock::initMock("Foo")->mockGetPermissions($this->once(), null, $permissions)->finalizeMock();
        $this->assertInstanceOf(MaskCollection::class, $resource->getPermissions());
        
        $resource = ResourceMock::initMock("Foo")->mockGetPermissions($this->once(), ["Foo", "Bar"], $permissions)->finalizeMock();
        $this->assertInstanceOf(MaskCollection::class, $resource->getPermissions(["Foo", "Bar"]));
        
        $resource = ResourceMock::initMock("Foo")->mockGetPermissions($this->once(), ["foo", "bar"], null)->finalizeMock();
        $this->expectException(InvalidResourcePermissionException::class);
        $resource->getPermissions(["foo", "bar"]);
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetPermissions_consecutive()
     */
    public function testMockGetPermissions_consecutive(): void
    {
        $permissions1 = MaskCollectionMock::initMock()->mockCount($this->once(), 1)->finalizeMock();
        $permissions2 = MaskCollectionMock::initMock()->mockCount($this->once(), 10)->finalizeMock();
        $resource = ResourceMock::initMock("Foo")
                            ->mockGetPermissions_consecutive(
                                $this->exactly(2), 
                                [null, $permissions1], [["Foo", "Bar", "Moz"], $permissions2])
                            ->finalizeMock();
        
        $this->assertSame(1, $resource->getPermissions()->count());
        $this->assertSame(10, $resource->getPermissions(["Foo", "Bar", "Moz"])->count());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetPermission()
     */
    public function testMockGetPermission(): void
    {
        $mask = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->mockGetValue($this->once(), 3)->finalizeMock();
        $resource = ResourceMock::initMock("Foo")->mockGetPermission($this->any(), "Foo", $mask)->finalizeMock();
        
        $this->assertSame("Foo", $resource->getPermission("Foo")->getIdentifier());
        $this->assertSame(3, $resource->getPermission("Foo")->getValue());
        
        $mask = null;
        $resource = ResourceMock::initMock("Foo")->mockGetPermission($this->once(), "Foo", $mask)->finalizeMock();
        
        $this->expectException(InvalidResourcePermissionException::class);
        $resource->getPermission("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetPermission_consecutive()
     */
    public function testMockGetPermission_consecutive(): void
    {
        $maskFoo = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->finalizeMock();
        $maskBar = MaskMock::initMock("Bar")->mockGetIdentifier($this->once())->finalizeMock();
        $maskMoz = MaskMock::initMock("Moz")->mockGetIdentifier($this->once())->finalizeMock();
        
        $resource = ResourceMock::initMock("Foo")
                        ->mockGetPermission_consecutive($this->exactly(3), ["Foo" => $maskFoo, "Bar" => $maskBar, "Moz" => $maskMoz])
                        ->finalizeMock();
        $this->assertSame("Foo", $resource->getPermission("Foo")->getIdentifier());
        $this->assertSame("Bar", $resource->getPermission("Bar")->getIdentifier());
        $this->assertSame("Moz", $resource->getPermission("Moz")->getIdentifier());
        
        $resource = ResourceMock::initMock("Foo")
                            ->mockGetPermission_consecutive($this->exactly(2), ["Foo" => $maskFoo, "Bar" => null])
                            ->finalizeMock();
        $this->expectException(InvalidResourcePermissionException::class);
        $resource->getPermission("Foo");
        $resource->getPermission("Bar");
        
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockHasPermission()
     */
    public function testMockHasPermission(): void
    {
        $resource = ResourceMock::initMock("Foo")->mockHasPermission($this->any(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($resource->hasPermission("Foo"));
        
        $resource = ResourceMock::initMock("Foo")->mockHasPermission($this->any(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($resource->hasPermission("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockHasPermission_consecutive()
     */
    public function testMockHasPermission_consecutive(): void
    {
        $resource = ResourceMock::initMock("Foo")
                        ->mockHasPermission_consecutive($this->exactly(3), ["Foo" => true, "Bar" => false, "Moz" => true])
                        ->finalizeMock();
        
        $this->assertTrue($resource->hasPermission("Foo"));
        $this->assertFalse($resource->hasPermission("Bar"));
        $this->assertTrue($resource->hasPermission("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockAddEntity()
     */
    public function testMockAddEntity(): void
    {
        $entity = EntityMock::initMock("Foo")->finalizeMock();
        $resource = ResourceMock::initMock("Foo")->mockAddEntity($this->any(), $entity)->finalizeMock();
        
        $this->assertNull($resource->addEntity($entity));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockAddEntity_consecutive()
     */
    public function testMockAddEntity_consecutive(): void
    {
        $entities = [
            EntityMock::initMock("Foo")->finalizeMock(),
            EntityMock::initMock("Bar")->finalizeMock()
        ];
        $resource = ResourceMock::initMock("Foo")->mockAddEntity_consecutive($this->exactly(2), $entities[0], $entities[1])->finalizeMock();
        
        $this->assertNull($resource->addEntity($entities[0]));
        $this->assertNull($resource->addEntity($entities[1]));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetEntities()
     */
    public function testMockGetEntities(): void
    {
        $foo = EntityMock::initMock("Foo")->mockGetName($this->once())->finalizeMock();
        $bar = EntityMock::initMock("Bar")->mockGetName($this->once())->finalizeMock();
        
        $resource = ResourceMock::initMock("Foo")->mockGetEntities($this->once(), [$foo, $bar])->finalizeMock();
        
        $loop = 0;
        
        foreach ($resource->getEntities() as $entity) {
            $this->assertInstanceOf(Entity::class, $entity);
            switch ($loop) {
                case 0:
                    $this->assertSame("Foo", $entity->getName());
                    break;
                case 1:
                    $this->assertSame("Bar", $entity->getName());
                    break;
            }
            $loop++;
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetEntity()
     */
    public function testMockGetEntity(): void
    {
        $entity = EntityMock::initMock("Foo")->finalizeMock();
        $resource = ResourceMock::initMock("Foo")->mockGetEntity($this->any(), "Foo", $entity)->finalizeMock();
        
        $this->assertInstanceOf(Entity::class, $resource->getEntity("Foo"));
        
        $resource = ResourceMock::initMock("Foo")->mockGetEntity($this->any(), "Foo", null)->finalizeMock();
        $this->expectException(InvalidEntityException::class);
        $resource->getEntity("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetEntity_consecutive()
     */
    public function testMockGetEntity_consecutive(): void
    {
        $entities = [
            "Foo"   =>  EntityMock::initMock("Foo")->finalizeMock(),
            "Bar"   =>  EntityMock::initMock("Bar")->finalizeMock(),
            "Moz"   =>  EntityMock::initMock("Moz")->finalizeMock()
        ];
        $resource = ResourceMock::initMock("Foo")->mockGetEntity_consecutive($this->exactly(3), $entities)->finalizeMock();
        
        $this->assertInstanceOf(Entity::class, $resource->getEntity("Foo"));
        $this->assertInstanceOf(Entity::class, $resource->getEntity("Bar"));
        $this->assertInstanceOf(Entity::class, $resource->getEntity("Moz"));
        
        $entities = [
            "Foo"   =>  EntityMock::initMock("Foo")->finalizeMock(),
            "Bar"   =>  null,
        ];
        $resource = ResourceMock::initMock("Foo")->mockGetEntity_consecutive($this->exactly(2), $entities)->finalizeMock();
        $this->expectException(InvalidEntityException::class);
        $resource->getEntity("Foo");
        $resource->getEntity("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetBehaviour()
     */
    public function testMockGetBehaviour(): void
    {
        $resource = ResourceMock::initMock("Foo")->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST_BEHAVIOUR)->finalizeMock();
        
        $this->assertSame(ResourceInterface::BLACKLIST_BEHAVIOUR, $resource->getBehaviour());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetName()
     */
    public function testMockGetName(): void
    {
        $resource = ResourceMock::initMock("Foo")->mockGetName($this->once())->finalizeMock();
        
        $this->assertSame("Foo", $resource->getName());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockJsonSerialize()
     */
    public function testMockJsonSerialize(): void
    {
        $resource = ResourceMock::initMock("Foo")->mockJsonSerialize($this->once(), ["Foo" => "Bar"])->finalizeMock();
        
        $this->assertSame(["Foo" => "Bar"], $resource->jsonSerialize());
    }
    
                        /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'getName' for mocked resource 'Foo' has been already mocked");
        
        $resource = ResourceMock::initMock("Foo")->mockGetName($this->once())->mockGetName($this->once())->finalizeMock();
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceMock::mockGetPermissions()
     */
    public function testExceptionOnInvalidBehaviourDuringMockingGetPermissions(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Cannot set Permissions and PermissionsReturned both null for mock resource 'Foo'");
        
        $resource = ResourceMock::initMock("Foo")->mockGetPermissions($this->once(), null, null);
    }
    
}
