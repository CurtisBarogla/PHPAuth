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
use Psr\SimpleCache\CacheInterface;
use Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader;
use ZoeTest\Component\Security\Mock\ResourceLoaderMock;
use ZoeTest\Component\Security\Mock\ResourceMock;
use Zoe\Component\Security\Acl\Resource\ResourceInterface;
use Zoe\Component\Security\Acl\Resource\Resource;

/**
 * CacheWrapperResourceLoader testcase
 * 
 * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class CacheWrapperResourceLoaderTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader::loadResource()
     */
    public function testLoadResource(): void
    {
        $resource = new Resource("Foo", ResourceInterface::BLACKLIST_BEHAVIOUR);
        $loader = ResourceLoaderMock::initMock()->mockLoadResource($this->once(), "Foo", $resource)->finalizeMock();
        $cache = $this->getMockBuilder(CacheInterface::class)->setMethods($this->getPSR16MethodsInterface())->getMock();
        $cache->expects($this->once())->method("get")->with(CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Foo")->will($this->returnValue(null));
        $resource = \json_encode($resource);
        $cache->expects($this->once())->method("set")->with(CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Foo", $resource)->will($this->returnValue(true));
        $wrapper = new CacheWrapperResourceLoader($loader, $cache);
        
        $this->assertInstanceOf(ResourceInterface::class, $wrapper->loadResource("Foo"));
        $this->assertInstanceOf(ResourceInterface::class, $wrapper->loadResource("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader::loadResource()
     */
    public function testLoadResourceFromCache(): void
    {
        $resource = ResourceMock::initMock("Foo")->finalizeMock();
        $loader = ResourceLoaderMock::initMock()->mockLoadResource($this->never(), "Foo", $resource)->finalizeMock();
        $json = '{"name":"Foo","behaviour":0,"permissions":{"identifier":"PERMISSIONS_Foo","masks":{"foo":{"identifier":"foo","value":1},"bar":{"identifier":"bar","value":2}}},"entities":[]}';
        $cache = $this->getMockBuilder(CacheInterface::class)->setMethods($this->getPSR16MethodsInterface())->getMock();
        $cache->expects($this->once())->method("get")->with(CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Foo")->will($this->returnValue($json));
        $wrapper = new CacheWrapperResourceLoader($loader, $cache);
        $reflection = new \ReflectionClass($wrapper);
        
        $this->assertInstanceOf(ResourceInterface::class, $wrapper->loadResource("Foo"));
        $this->assertInstanceOf(ResourceInterface::class, $wrapper->loadResource("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader::register()
     */
    public function testRegister(): void
    {
        $cacheValue = '["Acl_Cache_Resource_Foo","Acl_Cache_Resource_Bar","Acl_Cache_Resource_Moz"]';
        $loaderValue = [
            CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Foo",
            CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Bar",
            CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Moz",
        ];
        $loader = ResourceLoaderMock::initMock()->mockRegister($this->once(), $loaderValue)->finalizeMock();
        $cache = $this->getMockBuilder(CacheInterface::class)->setMethods($this->getPSR16MethodsInterface())->getMock();
        $cache->expects($this->once())->method("get")->with(CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Cached_Map_Names")->will($this->returnValue(null));
        $cache->expects($this->once())->method("set")->with(CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Cached_Map_Names", $cacheValue)->will($this->returnValue(true));
        
        $wrapper = new CacheWrapperResourceLoader($loader, $cache);
        
        $this->assertSame($loaderValue, $wrapper->register());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader::register()
     */
    public function testRegisterFromCache(): void
    {
        $cacheValue = '["Acl_Cache_Resource_Foo","Acl_Cache_Resource_Bar","Acl_Cache_Resource_Moz"]';
        $loader = ResourceLoaderMock::initMock()->mockRegister($this->never(), [])->finalizeMock();
        $cache = $this->getMockBuilder(CacheInterface::class)->setMethods($this->getPSR16MethodsInterface())->getMock();
        $cache->expects($this->once())->method("get")->with(CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Cached_Map_Names")->will($this->returnValue($cacheValue));
        $wrapper = new CacheWrapperResourceLoader($loader, $cache);
        
        $this->assertSame(
        [
            CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Foo",
            CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Bar",
            CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Moz",
        ], $wrapper->register());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Resource\Loader\CacheWrapperResourceLoader::clear()
     */
    public function testClear(): void
    {
        $keys = ["Acl_Cache_Resource_Foo","Acl_Cache_Resource_Bar","Acl_Cache_Resource_Moz"];
        $loader = ResourceLoaderMock::initMock()->mockRegister($this->once(), ["Foo", "Bar", "Moz"])->finalizeMock();
        $keys[] = CacheWrapperResourceLoader::CACHE_RESOURCE_PREFIX."_Cached_Map_Names";
        $cache = $this->getMockBuilder(CacheInterface::class)->setMethods($this->getPSR16MethodsInterface())->getMock();
        $cache->expects($this->once())->method("deleteMultiple")->with($keys)->will($this->returnValue(true));
        
        $wrapper = new CacheWrapperResourceLoader($loader, $cache);
        
        $this->assertTrue($wrapper->clear());
    }
    
    /**
     * Get all methods from PSR-16 interface
     * 
     * @return array
     *   All method from cache PSR-16
     */
    private function getPSR16MethodsInterface(): array
    {
        $reflection = new \ReflectionClass(CacheInterface::class);
        
        return $this->reflection_extractMethods($reflection);
    }
    
}
