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

namespace ZoeTest\Component\Security\MockGeneration\Tests\Acl;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceLoaderMock;
use ZoeTest\Component\Security\MockGeneration\Acl\ResourceMock;
use Zoe\Component\Security\Exception\Acl\ResourceNotFoundException;

/**
 * ResourceLoaderMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceLoaderMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceLoaderMockTest extends TestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\ResourceLoaderMock::mockLoadResource()
     */
    public function testMockLoadResource(): void
    {
        $resourceLoaded = ResourceMock::init("ResourceLoaded")->finalizeMock();
        $loader = ResourceLoaderMock::init("Foo")->mockLoadResource($this->once(), "Foo", $resourceLoaded)->finalizeMock();
        
        $this->assertSame($resourceLoaded, $loader->loadResource("Foo"));
        
        $this->expectException(ResourceNotFoundException::class);
        $loader = ResourceLoaderMock::init("Foo")->mockLoadResource($this->once(), "Foo", null)->finalizeMock();
        $loader->loadResource("Foo");
    }
    
}
