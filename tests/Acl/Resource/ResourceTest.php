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
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Exception\Acl\InvalidResourceBehaviour;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use Zoe\Component\Security\Common\JsonSerializable;
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use Zoe\Component\Security\Exception\Acl\InvalidEntityException;
use Zoe\Component\Security\Acl\Entity\Entity;

/**
 * Resource testcase
 * 
 * @see \Zoe\Component\Security\Acl\Resource\Resource
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource
     */
    public function testInterface(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $this->assertInstanceOf(JsonSerializable::class, $resource);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getName()
     */
    public function testGetName(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $this->assertSame("Foo", $resource->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::addPermission()
     */
    public function testAddPermission(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $this->assertNull($resource->addPermission("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getPermissions()
     */
    public function testGetPermissions(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->addPermission("Foo");
        $resource->addPermission("Bar");
        $resource->addPermission("Moz");
        
        $this->assertInstanceOf(MaskCollection::class, $resource->getPermissions());
        $this->assertInstanceOf(MaskCollection::class, $resource->getPermissions(["Foo", "Bar"]));
        $this->assertSame(3, \count($resource->getPermissions()));
        $this->assertSame(2, \count($resource->getPermissions(["Foo", "Bar"])));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getPermission()
     */
    public function testGetPermission(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->addPermission("Foo");
        $resource->addPermission("Bar");
        $resource->addPermission("Moz");
        
        $this->assertSame(1, $resource->getPermission("Foo")->getValue());
        $this->assertSame(2, $resource->getPermission("Bar")->getValue());
        $this->assertSame(4, $resource->getPermission("Moz")->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::hasPermission()
     */
    public function testHasPermission(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $this->assertFalse($resource->hasPermission("Foo"));
        
        $resource->addPermission("Foo");
        
        $this->assertTrue($resource->hasPermission("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::addEntity()
     */
    public function testAddEntity(): void
    {
        $entity = EntityMock::init("AddedToResource")->finalizeMock();
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $this->assertNull($resource->addEntity($entity));
    }
    
    public function testGetEntities(): void
    {
        $fooEntity = EntityMock::init("EntityFooAdded")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        $barEntity = EntityMock::init("EntityBarAdded")->mockGetIdentifier($this->once(), "Bar")->finalizeMock();
        
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->addEntity($fooEntity);
        $resource->addEntity($barEntity);
        $expected = ["Foo" => $fooEntity, "Bar" => $barEntity];
        
        $this->assertSame($expected, $resource->getEntities());
        
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $this->assertNull($resource->getEntities());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getEntity()
     */
    public function testGetEntity(): void
    {
        $entity = EntityMock::init("GettedFromResource")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $resource->addEntity($entity);
        
        $this->assertSame($entity, $resource->getEntity("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getBehaviour()
     */
    public function testGetBehaviour(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $this->assertSame(ResourceInterface::BLACKLIST, $resource->getBehaviour());
        
        $resource = new Resource("Foo", ResourceInterface::WHITELIST);
        
        $this->assertSame(ResourceInterface::WHITELIST, $resource->getBehaviour());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->addPermission("Foo");
        $resource->addPermission("Bar");
        $resource->addPermission("Moz");
        $entityFoo = new Entity("Foo");
        $entityBar = new Entity("Bar");
        $resource->addEntity($entityFoo);
        $resource->addEntity($entityBar);
        
        $this->assertNotFalse(\json_encode($resource));
    }
    
    public function testResourceFromJson(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->addPermission("Foo");
        $resource->addPermission("Bar");
        $resource->addPermission("Moz");
        $entityFoo = new Entity("Foo", "FooProcessor");
        $entityBar = new Entity("Bar");
        $entityFoo->add("Foo", ["Foo", "Bar"]);
        $entityBar->add("Foo", ["Foo", "Bar"]);
        
        $resource->addEntity($entityFoo);
        $resource->addEntity($entityBar);
        
        $json = \json_encode($resource);
        
        $this->assertEquals($resource, Resource::restoreFromJson($json));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::__construct()
     */
    public function testExceptionWhenBehaviourIsInvalid(): void
    {
        $this->expectException(InvalidResourceBehaviour::class);
        $this->expectExceptionMessage("Given behaviour is invalid for 'Foo' resource");
        
        $resource = new Resource("Foo", 3);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::addPermission()
     */
    public function testExceptionAddPermissionWhenMaxIsReached(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Cannot add more permission for 'Foo' resource");
        
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        for ($i = 0; $i <= ResourceInterface::MAX_PERMISSIONS + 1; $i++) {
            $resource->addPermission((string) $i);
        }
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getPermissions()
     */
    public function testExceptionGetPermissionsWhenAPermissionIsNotSetted(): void
    {
        $this->expectException(InvalidPermissionException::class);
        $this->expectExceptionMessage("This permission 'Bar' is not defined into 'Foo' resource");
        
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->getPermissions(["Bar"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getPermission()
     */
    public function testExceptionGetPermissionWhenPermissionIsNotSetted(): void
    {
        $this->expectException(InvalidPermissionException::class);
        $this->expectExceptionMessage("This permission 'Bar' is not defined into 'Foo' resource");
        
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $resource->getPermission("Bar");
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getEntity()
     */
    public function testExceptionGetEntityWhenNotRegistered(): void
    {
        $this->expectException(InvalidEntityException::class);
        $this->expectExceptionMessage("This entity 'Foo' for resource 'Bar' is not registered");
        
        $resource = new Resource("Bar", ResourceInterface::BLACKLIST);
        $resource->getEntity("Foo");
    }
}
