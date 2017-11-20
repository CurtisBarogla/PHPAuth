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

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Exception\InvalidResourcePermissionException;

/**
 * Resource testcase
 * 
 * @see \Zoe\Component\Security\Acl\Resource\Resource
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::addPermission()
     */
    public function testAddPermission(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        
        $this->assertNull($resource->addPermission("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getPermissions()
     */
    public function testGetPermissions(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        
        $resource->addPermission("foo");
        $resource->addPermission("bar");
        $resource->addPermission("moz");
        $resource->addPermission("poz");
        
        $expected = new MaskCollection(ResourceInterface::PERMISSIONS_IDENTIFIER."foo");
        $expected->add(new Mask("foo", 0x0001));
        $expected->add(new Mask("bar", 0x0002));
        $expected->add(new Mask("moz", 0x0004));
        $expected->add(new Mask("poz", 0x0008));
        
        $this->assertEquals($expected, $resource->getPermissions());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getPermission()
     */
    public function testGetPermission(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        
        $resource->addPermission("foo");
        
        $this->assertEquals(new Mask("foo", 0x0001), $resource->getPermission("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::hasPermission()
     */
    public function testHasPermission(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        
        $this->assertFalse($resource->hasPermission("foo"));
        
        $resource->addPermission("foo");
        
        $this->assertTrue($resource->hasPermission("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getBehaviour()
     */
    public function testGetBehaviour(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        
        $this->assertSame(ResourceInterface::BLACKLIST_BEHAVIOUR, $resource->getBehaviour());
        
        $resource = new Resource("foo", ResourceInterface::WHITELIST_BEHAVIOUR);
        
        $this->assertSame(ResourceInterface::WHITELIST_BEHAVIOUR, $resource->getBehaviour());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getName()
     */
    public function testGetName(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        
        $this->assertSame("foo", $resource->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        $resource->addPermission("foo");
        $resource->addPermission("bar");
        
        $this->assertNotFalse(\json_encode($resource));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::createResourceFromJson()
     */
    public function testCreateResourceFromJson(): void
    {
        $resource = new Resource("foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        $resource->addPermission("foo");
        $resource->addPermission("bar");
        $resource->addPermission("moz");
        
        $json = \json_encode($resource);
        
        $this->assertEquals($resource, Resource::createResourceFromJson($json));
        
        $json = \json_decode($json, true);
        
        $this->assertEquals($resource, Resource::createResourceFromJson($json));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Resource::getPermission()
     */
    public function testExceptionWhenGettingAnInvalidPermission(): void
    {
        $this->expectException(InvalidResourcePermissionException::class);
        $this->expectExceptionMessage("This permission 'foo' for the resource 'bar' is not registered");
        
        $resource = new Resource("bar", ResourceInterface::BLACKLIST_BEHAVIOUR);
        
        $resource->getPermission("foo");
    }
    
}
