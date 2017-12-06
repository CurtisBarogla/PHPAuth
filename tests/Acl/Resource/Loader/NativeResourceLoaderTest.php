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

namespace ZoeTest\Component\Security\Acl\Resource\Loader;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Entity\Entity;
use Zoe\Component\Security\Exception\ResourceNotFoundException;

/**
 * NativeResourceLoader testcase
 * 
 * @see \Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeResourceLoaderTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader::loadResource()
     */
    public function testLoadResource(): void
    {
        $resources = [
            "Foo"   =>  [
                "permissions"   =>  ["foo", "bar", "moz"],
                "behaviour"     =>  "blacklist",
                "entities"      =>  [
                    "Foo"           =>  [
                        "processor"     =>  "Foo",
                        "values"        =>  [
                            "foo"           =>  ["foo", "bar"]
                        ]
                    ]
                ]
            ],
            "Bar"   =>  [
                "permissions"   =>  ["moz", "poz", "loz"],
                "behaviour"     =>  "whitelist",
                "entities"      =>  [
                    "Foo"           =>  [
                        "values"        =>  [
                            "foo"           =>  ["moz", "loz"],
                            "bar"           =>  ["poz", "moz"]
                        ]
                    ]
                ]
            ]
        ];
        
        $loader = new NativeResourceLoader($resources);
        $foo = $loader->loadResource("Foo");
        $bar = $loader->loadResource("Bar");
        
        $this->assertSame("Foo", $foo->getName());
        $this->assertSame(ResourceInterface::BLACKLIST_BEHAVIOUR, $foo->getBehaviour());
        $this->assertInstanceOf(MaskCollection::class, $foo->getPermissions());
        $this->assertTrue($foo->hasPermission("foo"));
        $this->assertTrue($foo->hasPermission("bar"));
        $this->assertTrue($foo->hasPermission("moz"));
        $this->assertSame("Foo", $foo->getEntity("Foo")->getProcessor());
        
        $this->assertSame("Bar", $bar->getName());
        $this->assertSame(ResourceInterface::WHITELIST_BEHAVIOUR, $bar->getBehaviour());
        $this->assertInstanceOf(MaskCollection::class, $bar->getPermissions());
        $this->assertTrue($bar->hasPermission("moz"));
        $this->assertTrue($bar->hasPermission("poz"));
        $this->assertTrue($bar->hasPermission("loz"));
        $this->assertInstanceOf(Entity::class, $bar->getEntity("Foo"));
        $this->assertSame(["moz", "loz"], $bar->getEntity("Foo")->get("foo"));
        $this->assertNull($bar->getEntity("Foo")->getProcessor());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader::register()
     */
    public function testRegister(): void
    {
        $resources = [
            "Foo"   =>  [
                "permissions"   =>  ["foo", "bar", "moz"],
                "behaviour"     =>  "blacklist"
            ],
            "Bar"   =>  [
                "permissions"   =>  ["moz", "poz", "loz"],
                "behaviour"     =>  "whitelist"
            ]
        ];
        
        $loader = new NativeResourceLoader($resources);
        
        $this->assertSame(["Foo", "Bar"], $loader->register());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader::loadResource()
     */
    public function testExceptionWhenResourceCannotBeLoaded(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage("This resource 'Foo' cannot be loaded");
        $resources = [];
        
        $loader = new NativeResourceLoader($resources);
        $loader->loadResource("Foo");
    }
    
}
