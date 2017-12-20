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

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\Exception\Acl\ResourceNotFoundException;
use Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;

/**
 * NativeResourceLoader testcase
 * 
 * @see \Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeResourceLoaderTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader::loadResource()
     */
    public function testLoadResource(): void
    {
        $resources = [
            "Foo"   =>  [
                "behaviour"     =>  ResourceInterface::WHITELIST,
                "permissions"   =>  ["Foo", "Bar"]
            ],
            "Bar"   =>  [
                "behaviour"     =>  ResourceInterface::BLACKLIST,
                "permissions"   =>  ["Moz", "Poz"],
                "entities"      =>  [
                    "FooEntity"    =>  [
                        "processor"     =>  "FooProcessor",
                        "values"        =>  [
                            "FOO"           =>  ["Moz"],
                            "BAR"           =>  ["Poz"]
                        ]                        
                    ],
                    "BarEntity"     =>  [
                        "values"        =>  [
                            "MOZ"           =>  ["Poz"],
                            "POZ"           =>  ["Moz", "Poz"]
                        ]
                    ]
                ]
            ],
        ];
        
        $loader = new NativeResourceLoader($resources);
        $foo = $loader->loadResource("Foo");
        $bar = $loader->loadResource("Bar");
        
        // Foo
        $this->assertSame(ResourceInterface::WHITELIST, $foo->getBehaviour());
        $this->assertSame(1, $foo->getPermission("Foo")->getValue());
        $this->assertSame(2, $foo->getPermission("Bar")->getValue());
        $this->assertNull($foo->getEntities());
        
        //Bar
        $this->assertSame(ResourceInterface::BLACKLIST, $bar->getBehaviour());
        $this->assertSame(1, $bar->getPermission("Moz")->getValue());
        $this->assertSame(2, $bar->getPermission("Poz")->getValue());
        $this->assertCount(2, $bar->getEntities());
        $this->assertSame("FooProcessor", $bar->getEntity("FooEntity")->getProcessor());
        $this->assertNull($bar->getEntity("BarEntity")->getProcessor());
        $this->assertSame(["Moz"], $bar->getEntity("FooEntity")->get("FOO"));
        $this->assertSame(["Poz"], $bar->getEntity("FooEntity")->get("BAR"));
        $this->assertSame(["Poz"], $bar->getEntity("BarEntity")->get("MOZ"));
        $this->assertSame(["Moz", "Poz"], $bar->getEntity("BarEntity")->get("POZ"));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\NativeResourceLoader::loadResource()
     */
    public function testExceptionLoadResourceWhenResourceIsNotFound(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage("This resource 'Foo' cannot be loaded");
        
        $loader = new NativeResourceLoader([]);
        $loader->loadResource("Foo");
    }
    
}
