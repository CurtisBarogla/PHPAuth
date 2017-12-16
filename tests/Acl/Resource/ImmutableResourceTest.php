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
     * @see \Zoe\Component\Security\Acl\Resource\ImmutableResource::getBehaviour()
     */
    public function testGetBehaviour(): void
    {
        $mutable = $this->getMockedResource();
        $immutable = new ImmutableResource($mutable);
        
        $this->assertSame(ResourceInterface::BLACKLIST, $immutable->getBehaviour());
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
        
        return ResourceMock::init("ResourceWrappedToimmutable", ResourceInterface::class)
                                ->mockGetName($this->once(), "Foo")
                                ->mockAddPermission($this->never(), "Foo")
                                ->mockGetPermissions($this->once(), ["Foo", "Bar"], $permissions)
                                ->mockGetPermission($this->once(), "Foo", $permission)
                                ->mockHasPermission($this->once(), "Foo", true)
                                ->mockGetBehaviour($this->once(), ResourceInterface::BLACKLIST)
                            ->finalizeMock();     
    }
    
}
