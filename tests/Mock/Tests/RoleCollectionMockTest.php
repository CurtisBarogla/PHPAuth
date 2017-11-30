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
use ZoeTest\Component\Security\Mock\RoleCollectionMock;

/**
 * RoleCollectionMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\RoleCollectionMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleCollectionMockTest extends SecurityTestCase
{
     
    /**
     * @see \ZoeTest\Component\Security\Mock\RoleCollectionMock::mockGet()
     */
    public function testMockGet(): void
    {
        $collection = RoleCollectionMock::initMock()->mockGet($this->any(), "Foo", ["Foo", "Bar"])->finalizeMock();
        
        $this->assertSame(["Foo", "Bar"], $collection->get("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\RoleCollectionMock::mockGet_consecutive()
     */
    public function testMockGet_consecutive(): void
    {
        $roles = [
            "Foo"   =>  ["Foo"],
            "Bar"   =>  ["Bar", "Foo"],
            "Moz"   =>  ["Moz", "Bar", "Foo"]
        ];
        $collection = RoleCollectionMock::initMock()->mockGet_consecutive($this->exactly(3), $roles)->finalizeMock();
        
        $this->assertSame(["Foo"], $collection->get("Foo"));
        $this->assertSame(["Bar", "Foo"], $collection->get("Bar"));
        $this->assertSame(["Moz", "Bar" ,"Foo"], $collection->get("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\RoleCollectionMock::mockHas()
     */
    public function testMockHas(): void
    {
        $collection = RoleCollectionMock::initMock()->mockHas($this->any(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($collection->has("Foo"));
        
        $collection = RoleCollectionMock::initMock()->mockHas($this->any(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($collection->has("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\RoleCollectionMock::mockHas_consecutive()
     */
    public function testMockHas_consecutive(): void
    {
        $collection = RoleCollectionMock::initMock()
                                ->mockHas_consecutive($this->exactly(3), ["Foo" => true, "Bar" => false, "Moz" => true])
                                ->finalizeMock();
        
        $this->assertTrue($collection->has("Foo"));
        $this->assertFalse($collection->has("Bar"));
        $this->assertTrue($collection->has("Moz"));
    }
    
                        /**______EXCEPTIONS______**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\RoleCollectionMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'has' has been already mocked for this mocked role collection");
        
        $collection = RoleCollectionMock::initMock()
                                ->mockHas($this->any(), "Foo", false)
                                ->mockHas($this->any(), "Foo", false)
                                ->finalizeMock();
    }
    
}
