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
use ZoeTest\Component\Security\MockGeneration\Acl\EntityMock;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Internal\ReflectionTrait;
use Zoe\Component\Security\Acl\AclUserInterface;
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Acl\Entity\Processor\EntityProcessorInterface;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Common\JsonSerializable;
use Zoe\Component\Security\Exception\Acl\InvalidEntityException;
use Zoe\Component\Security\Exception\Acl\InvalidPermissionException;
use Zoe\Component\Security\Exception\Acl\InvalidResourceBehaviour;

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
    
    use ReflectionTrait;
    
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
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getEntities()
     */
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
     * @see \Zoe\Component\Security\Acl\Resource\Resource::process()
     */
    public function testProcess(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        $user = UserMock::init("AclUser", AclUserInterface::class)->finalizeMock();
        
        $entityFoo = EntityMock::init("EmptyEntity")
                                    ->mockGetIdentifier($this->once(), "Foo")
                                    ->mockGetProcessor($this->once(), "FooProcessor")    
                                    ->mockIsEmpty($this->once(), true)
                                ->finalizeMock();
        $entityBar = EntityMock::init("NullProcessorEntity")
                                    ->mockGetIdentifier($this->once(), "Bar")
                                    ->mockGetProcessor($this->once(), null)
                                    ->mockIsEmpty($this->once(), false)
                                ->finalizeMock();
        $entityMoz = EntityMock::init("EntityProcessed1")
                                    ->mockGetIdentifier($this->once(), "Moz")
                                    ->mockIsEmpty($this->once(), false)
                                    ->mockGetProcessor($this->once(), "FooProcessor")
                                ->finalizeMock();
        
        $resource->addEntity($entityFoo);
        $resource->addEntity($entityMoz);
        $resource->addEntity($entityBar);
        
        // resource injection via interface
        $concreteEntity = new Entity("Concrete", "FooProcessor");
        $concreteEntity->add("Foo", ["Foo", "Bar"]);

        $methods = $this->reflection_extractMethods(new \ReflectionClass(EntityProcessorInterface::class));
        $processor = $this->getMockBuilder(EntityProcessorInterface::class)->setMethods($methods)->getMock();
        $processor
                ->expects($this->exactly(2))
                ->method("process")
                ->withConsecutive([$entityMoz, $user], [$concreteEntity, $user])
                ->willReturnOnConsecutiveCalls($this->returnValue(null));
        
        $processors = ["FooProcessor" => $processor];

        $resource->addEntity($concreteEntity);
        
        $this->assertNull($resource->process($user, $processors));
        $this->assertTrue($resource->isProcessed());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::isProcessed()
     */
    public function testIsProcessed(): void
    {
        $user = UserMock::init("AclUserProcessed", AclUserInterface::class)->finalizeMock();
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST);
        
        $this->assertFalse($resource->isProcessed());
        
        $resource->process($user, []);
        
        $this->assertTrue($resource->isProcessed());
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
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::process()
     */
    public function testExceptionProcessWhenAnEntityHasANonRegisteredProcessor(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("This processor 'InvalidProcessor' for 'Foo' entity into 'Bar' resource is not registered");
        
        $entity = EntityMock::init("InvalidProcessor")
                                ->mockGetIdentifier($this->exactly(2), "Foo")
                                ->mockIsEmpty($this->once(), false)
                                ->mockGetProcessor($this->once(), "InvalidProcessor")
                            ->finalizeMock();
        $user = UserMock::init("AclUser", AclUserInterface::class)->finalizeMock();
        
        $resource = new Resource("Bar", ResourceInterface::BLACKLIST);
        $resource->addEntity($entity);
        $resource->process($user, []);
    }
    
}
