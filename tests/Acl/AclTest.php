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

namespace ZoeTest\Component\Security\Acl;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\ResourceLoaderMock;
use ZoeTest\Component\Security\Mock\EntityProcessorMock;
use Zoe\Component\Security\Acl\Acl;
use ZoeTest\Component\Security\Mock\ResourceMock;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\User\Contracts\AclUserInterface;
use ZoeTest\Component\Security\Mock\MaskMock;
use ZoeTest\Component\Security\Mock\EntityMock;
use Zoe\Component\Security\Exception\RuntimeException;
use Zoe\Component\Security\Acl\AclBindableInterface;
use ZoeTest\Component\Security\Mock\MaskCollectionMock;

/**
 * Acl testcase
 * 
 * @see \Zoe\Component\Security\Acl\Acl
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AclTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::addEntityProcessor()
     */
    public function testAddEntityProcess(): void
    {
        $loader = ResourceLoaderMock::initMock()->finalizeMock();
        $processor = EntityProcessorMock::initMock("Foo")->mockGetName($this->once())->finalizeMock();
        
        $acl = new Acl($loader);
        
        $this->assertNull($acl->addEntityProcessor($processor));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::executeProcessables()
     */
    public function testExecuteProcessable(): void
    {
        $user = UserMock::initMock(AclUserInterface::class, "FooUser")->finalizeMock();
        $entity = EntityMock::initMock("FooEntity")
                        ->mockGetName($this->once())
                        ->mockGetProcessor($this->once(), "FooProcessor")
                    ->finalizeMock();
        $resource = ResourceMock::initMock("FooResource")
                        ->mockGetEntities($this->once(), [$entity])        
                    ->finalizeMock();
        $processor = EntityProcessorMock::initMock("FooProcessor")
                        ->mockGetName($this->once())
                        ->mockProcessUser($this->once(), $user, $resource, $entity)
                    ->finalizeMock();
        $loader = ResourceLoaderMock::initMock()
                        ->mockRegister($this->once(), ["FooResource"])
                        ->mockLoadResource($this->once(), "FooResource", $resource)
                    ->finalizeMock();
                        
        $acl = new Acl($loader);
        $acl->addEntityProcessor($processor);
        
        $this->assertNull($acl->executeProcessables($user));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::getResource()
     */
    public function testGetResource(): void
    {
        $resource = ResourceMock::initMock("Foo")->mockGetName($this->once())->finalizeMock();
        $loader = ResourceLoaderMock::initMock()->mockLoadResource($this->once(), "Foo", $resource)->finalizeMock();
        
        $acl = new Acl($loader);
        
        $resource = $acl->getResource("Foo");
        $this->assertInstanceOf(ResourceInterface::class, $resource);
        $this->assertSame("Foo", $resource->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::getResources()
     */
    public function testGetResources(): void
    {
        $resourceFoo = ResourceMock::initMock("Foo")
                            ->mockGetName($this->exactly(2))
                        ->finalizeMock();
        $resourceBar = ResourceMock::initMock("Bar")
                            ->mockGetName($this->exactly(2))
                        ->finalizeMock();
        $loader = ResourceLoaderMock::initMock()
                            ->mockRegister($this->once(), ["Foo", "Bar"])
                            ->mockLoadResource_consecutive($this->exactly(2), ["Foo" => $resourceFoo, "Bar" => $resourceBar])
                        ->finalizeMock();
        
        $acl = new Acl($loader);
        $resources = $acl->getResources();
        $this->assertSame("Foo", $resources["Foo"]->getName());
        $this->assertSame("Bar", $resources["Bar"]->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::isGranted()
     */
    public function testIsGranted(): void
    {
        $mask = MaskMock::initMock("PERMISSIONS")
                            ->mockGetValue($this->once(), 4)
                        ->finalizeMock();    
        $permissions = MaskCollectionMock::initMock()
                            ->mockTotal($this->once(), "PERMISSIONS", $mask)
                        ->finalizeMock();
        $resource = ResourceMock::initMock("Foo")
                            ->mockGetName($this->exactly(3))
                            ->mockGetPermissions($this->once(), ["foo", "bar"], $permissions)
                        ->finalizeMock();
        $maskUser = MaskMock::initMock("Foo")
                            ->mockGetIdentifier($this->once())
                            ->mockGetValue($this->once(), 7)
                        ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                            ->mockGetPermission($this->once(), "Foo", $maskUser)
                        ->finalizeMock();
        $loader = ResourceLoaderMock::initMock()
                            ->mockLoadResource($this->exactly(3), "Foo", $resource)
                        ->finalizeMock();
        
        $acl = new Acl($loader);
        
        $this->assertTrue($acl->isGranted($user, "Foo", ["foo", "bar"]));
        
        $maskUser = MaskMock::initMock("Foo")
                            ->mockGetIdentifier($this->exactly(2))
                            ->mockGetValue($this->exactly(2), 3)
                        ->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")
                            ->mockGetPermission($this->exactly(2), "Foo", $maskUser)
                        ->finalizeMock();
        
        $this->assertFalse($acl->isGranted($user, "Foo", ["foo", "bar"]));
        
        $reflection = new \ReflectionClass(AclBindableInterface::class);
        $bindable = $this->getMockBuilder(AclBindableInterface::class)->setMethods($this->reflection_extractMethods($reflection))->getMock();
        $bindable->expects($this->once())->method("_getName")->will($this->returnValue("Foo"));
        $bindable->expects($this->once())->method("_onBind")->with($user, $resource)->will($this->returnValue(null));
        
        $acl->bind($bindable);
        
        $this->assertFalse($acl->isGranted($user, "Foo", ["foo", "bar"]));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::isGranted()
     */
    public function testIsGrantedWhenResourceNotRegisteredFromUserOrAcl(): void
    {
        // resource not loaded
        $loader = ResourceLoaderMock::initMock()->mockLoadResource($this->once(), "Foo", null)->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->finalizeMock();
        $acl = new Acl($loader);
        
        $this->assertFalse($acl->isGranted($user, "Foo", ["foo"]));
        
        // user permissions not setted
        $resource = ResourceMock::initMock("Foo")->finalizeMock();
        $loader = ResourceLoaderMock::initMock()->mockLoadResource($this->once(), "Foo", $resource)->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->mockGetPermission($this->once(), "Foo", null)->finalizeMock();
        
        $acl = new Acl($loader);
        
        $this->assertFalse($acl->isGranted($user, "Foo", ["foo"]));
        
        // permission into resource not setted
        $resource = ResourceMock::initMock("Foo")->mockGetPermissions($this->once(), ["foo", "bar"], null)->finalizeMock();
        $loader = ResourceLoaderMock::initMock()->mockLoadResource($this->once(), "Foo", $resource)->finalizeMock();
        $permission = MaskMock::initMock("Foo")->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->mockGetPermission($this->once(), "Foo", $permission)->finalizeMock();
        
        $acl = new Acl($loader);
        
        $this->assertFalse($acl->isGranted($user, "Foo", ["foo", "bar"]));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::isGranted()
     */
    public function testIsGrantedWhenEmpty(): void
    {
        $loader = ResourceLoaderMock::initMock()->finalizeMock();
        $user = UserMock::initMock(AclUserInterface::class, "Foo")->finalizeMock();
        
        $acl = new Acl($loader);
        
        $this->assertTrue($acl->isGranted($user, "Foo", []));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::bind()
     */
    public function testBind(): void
    {
        $reflection = new \ReflectionClass(AclBindableInterface::class);
        $bindable = $this->getMockBuilder(AclBindableInterface::class)->setMethods($this->reflection_extractMethods($reflection))->getMock();
        $bindable->method("_getName")->will($this->returnValue("Foo"));
        $loader = ResourceLoaderMock::initMock()->finalizeMock();
        
        $acl = new Acl($loader);
        
        $this->assertNull($acl->bind($bindable));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Acl::executeProcessables()
     */
    public function testExceptionWhenProcessorIsNotRegistered(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("This processor 'FooProcessor' for entity 'FooEntity' on resource 'FooResource' is not registered into the acl");
    
        $user = UserMock::initMock(AclUserInterface::class, "FooUser")->finalizeMock();
        $entity = EntityMock::initMock("FooEntity")
                    ->mockGetName($this->once())
                    ->mockGetProcessor($this->once(), "FooProcessor")
                ->finalizeMock();
        $resource = ResourceMock::initMock("FooResource")
                    ->mockGetName($this->once())
                    ->mockGetEntities($this->once(), [$entity])
                ->finalizeMock();
        $processor = EntityProcessorMock::initMock("FooProcessor")
                    ->mockGetName($this->once())
                    ->mockProcessUser($this->never(), $user, $resource, $entity)
                ->finalizeMock();
        $loader = ResourceLoaderMock::initMock()
                    ->mockRegister($this->once(), ["FooResource"])
                    ->mockLoadResource($this->once(), "FooResource", $resource)
                ->finalizeMock();
        
        $acl = new Acl($loader);
        
        $this->assertNull($acl->executeProcessables($user));
    }
    
}
