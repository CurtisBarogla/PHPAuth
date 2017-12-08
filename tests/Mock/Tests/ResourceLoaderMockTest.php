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

namespace ZoeTest\Component\Security\Mock\Tests;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\ResourceLoaderMock;
use ZoeTest\Component\Security\Mock\ResourceMock;
use Zoe\Component\Security\Exception\ResourceNotFoundException;

/**
 * ResourceLoaderMock testcase 
 * 
 * @see \ZoeTest\Component\Security\Mock\ResourceLoaderMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class ResourceLoaderMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceLoaderMock::mockLoadResource()
     */
    public function testMockLoadResource(): void
    {
        $resource = ResourceMock::initMock("Foo")->mockGetName($this->once())->finalizeMock();
        $loader = ResourceLoaderMock::initMock()->mockLoadResource($this->once(), "Foo", $resource)->finalizeMock();
        
        $this->assertSame("Foo", $loader->loadResource("Foo")->getName());
        
        $this->expectException(ResourceNotFoundException::class);
        
        $loader = ResourceLoaderMock::initMock()->mockLoadResource($this->once(), "Foo", null)->finalizeMock();
        
        $loader->loadResource("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceLoaderMock::mockLoadResource_consecutive()
     */
    public function testMockLoadResource_consecutive(): void
    {
        $resourceFoo = ResourceMock::initMock("Foo")->mockGetName($this->exactly(2))->finalizeMock();
        $resourceBar = ResourceMock::initMock("Bar")->mockGetName($this->exactly(2))->finalizeMock();
        
        $loader = ResourceLoaderMock::initMock()
                                ->mockLoadResource_consecutive(
                                    $this->exactly(2), 
                                    ["Foo" => $resourceFoo, "Bar" => $resourceBar])
                            ->finalizeMock();
        
        $this->assertSame("Foo", $loader->loadResource("Foo")->getName());
        $this->assertSame("Bar", $loader->loadResource("Bar")->getName());
        
        $loader = ResourceLoaderMock::initMock()
                                ->mockLoadResource_consecutive($this->once(), ["Foo" => null])
                            ->finalizeMock();
        $this->expectException(ResourceNotFoundException::class);
        $loader->loadResource("Foo");
    }   
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceLoaderMock::mockRegister()
     */
    public function testMockRegister(): void
    {   
        $loader = ResourceLoaderMock::initMock()->mockRegister($this->once(), ["Foo", "Bar", "Poz"])->finalizeMock();
        
        $this->assertSame(["Foo", "Bar", "Poz"], $loader->register());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\ResourceLoaderMock)
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'register' for this resource loader has been already mocked");
        
        $loader = ResourceLoaderMock::initMock()
                        ->mockRegister($this->once(), ["Foo", "Bar", "Poz"])
                        ->mockRegister($this->once(), ["Foo", "Bar", "Poz"])
                    ->finalizeMock();
        
    }
    
}
