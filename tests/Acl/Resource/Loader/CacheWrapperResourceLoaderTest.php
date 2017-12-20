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
use Psr\SimpleCache\CacheInterface;
use Zoe\Component\Internal\ReflectionTrait;
use Zoe\Component\Security\Acl\Resource\Resource;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceLoaderMock;
use Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader;

/**
 * CacheWrapperResourceLoader testcase
 * 
 * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class CacheWrapperResourceLoaderTest extends TestCase
{
    
    use ReflectionTrait;
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader::loadResource()
     */
    public function testLoadResourceWithCache(): void
    {
        $expected = new Resource("Foo", ResourceInterface::BLACKLIST);
        $expected->addPermission("Foo");
        $resourceCached = \json_encode($expected);
        $reflection = new \ReflectionClass(CacheInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        $cache = $this->getMockBuilder(CacheInterface::class)->setMethods($methods)->getMock();
        $cache->expects($this->once())->method("get")->with("FOO_Foo")->will($this->returnValue($resourceCached));
        $cache->expects($this->never())->method("set");
        $wrapped = ResourceLoaderMock::init("ResourceLoaderWrapped")->mockLoadResource($this->never(), "Foo", null)->finalizeMock();
        
        $loader = new CacheWrapperResourceLoader($wrapped, $cache, "FOO");
        $this->assertEquals($expected, $loader->loadResource("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader::loadResource()
     */
    public function testLoadResourceWithoutCache(): void
    {
        $loaded = new Resource("Foo", ResourceInterface::BLACKLIST);
        $loaded->addPermission("Foo");
        $reflection = new \ReflectionClass(CacheInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        $cache = $this->getMockBuilder(CacheInterface::class)->setMethods($methods)->getMock();
        $cache->expects($this->once())->method("get")->with("FOO_Foo")->will($this->returnValue(null));
        $cache->expects($this->once())->method("set")->with("FOO_Foo", \json_encode($loaded))->will($this->returnValue(true));
        $wrapped = ResourceLoaderMock::init("ResourceLoaderWrapped")->mockLoadResource($this->once(), "Foo", $loaded)->finalizeMock();
        
        $loader = new CacheWrapperResourceLoader($wrapped, $cache, "FOO");
        $this->assertSame($loaded, $loader->loadResource("Foo"));
    }
    
}
