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
use ZoeTest\Component\Security\Mock\MaskCollectionMock;
use ZoeTest\Component\Security\Mock\MaskMock;
use Zoe\Component\Security\Exception\InvalidMaskException;

/**
 * MaskCollectionMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskCollectionMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockTotal()
     */
    public function testMockTotal(): void
    {
        $maskTotal = MaskMock::initMock("Foo")->mockGetValue($this->once(), 3)->finalizeMock();
        $collection = MaskCollectionMock::initMock()->mockTotal($this->any(), "Foo", $maskTotal)->finalizeMock();
        
        $this->assertSame(3, $collection->total("Foo")->getValue());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockAdd()
     */
    public function testMockAdd(): void
    {
        $mask = MaskMock::initMock("Foo")->finalizeMock();
        $collection = MaskCollectionMock::initMock()->mockAdd($this->once(), $mask)->finalizeMock();
        
        $this->assertNull($collection->add($mask));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockAdd_consecutive()
     */
    public function testMockAdd_consecutive(): void
    {
        $maskFoo = MaskMock::initMock("Foo")->finalizeMock();
        $maskBar = MaskMock::initMock("Bar")->finalizeMock();
        $maskMoz = MaskMock::initMock("Moz")->finalizeMock();
        $collection = MaskCollectionMock::initMock()->mockAdd_consecutive($this->exactly(3), $maskFoo, $maskBar, $maskMoz)->finalizeMock();
        
        $this->assertNull($collection->add($maskFoo));
        $this->assertNull($collection->add($maskBar));
        $this->assertNull($collection->add($maskMoz));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockGet()
     */
    public function testMockGet(): void
    {
        $returned = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->mockGetValue($this->once(), 3)->finalizeMock();
        $collection = MaskCollectionMock::initMock()->mockGet($this->exactly(2), "Foo", $returned)->finalizeMock();
        
        $this->assertSame("Foo", $collection->get("Foo")->getIdentifier());
        $this->assertSame(3, $collection->get("Foo")->getValue());
        
        $this->expectException(InvalidMaskException::class);
        
        $collection = MaskCollectionMock::initMock()->mockGet($this->once(), "Foo", null)->finalizeMock();
        $collection->get("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockGet_consecutive()
     */
    public function testMockGet_consecutive(): void
    {
        $maskFoo = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->mockGetValue($this->once(), 3)->finalizeMock();
        $maskBar = MaskMock::initMock("Bar")->mockGetIdentifier($this->once())->mockGetValue($this->once(), 3)->finalizeMock();
        $maskMoz = MaskMock::initMock("Moz")->mockGetIdentifier($this->once())->mockGetValue($this->once(), 3)->finalizeMock();
        $collection = MaskCollectionMock::initMock()
                            ->mockGet_consecutive($this->exactly(3), ["Foo" => $maskFoo, "Bar" => $maskBar, "Moz" => $maskMoz])
                            ->finalizeMock();
        
        $this->assertSame($maskFoo, $collection->get("Foo"));
        $this->assertSame($maskBar, $collection->get("Bar"));
        $this->assertSame($maskMoz, $collection->get("Moz"));
        
        $this->expectException(InvalidMaskException::class);
        $collection = MaskCollectionMock::initMock()
                            ->mockGet_consecutive($this->exactly(2), ["Foo" => $maskFoo, "Bar" => null])
                            ->finalizeMock();
        $collection->get("Foo");
        $collection->get("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockHas()
     */
    public function testMockHas(): void
    {
        $collection = MaskCollectionMock::initMock()->mockHas($this->once(), "Foo", true)->finalizeMock();    
        
        $this->assertTrue($collection->has("Foo"));
        
        $collection = MaskCollectionMock::initMock()->mockHas($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($collection->has("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockHas_conseuctive()
     */
    public function testMockHas_consecutive(): void
    {
        $collection = MaskCollectionMock::initMock()
                            ->mockHas_consecutive($this->exactly(3), ["Foo" => true, "Bar" => false, "Moz" => true])
                            ->finalizeMock();
        
        $this->assertTrue($collection->has("Foo"));
        $this->assertFalse($collection->has("Bar"));
        $this->assertTrue($collection->has("Moz"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockRefresh()
     */
    public function testMockRefresh(): void
    {
        $mask = MaskMock::initMock("Foo")->finalizeMock();
        $collection = MaskCollectionMock::initMock()->mockRefresh($this->once(), $mask, false)->finalizeMock();
        
        $this->assertNull($collection->refresh($mask));
        
        $collection = MaskCollectionMock::initMock()->mockRefresh($this->once(), $mask, true)->finalizeMock();
        $this->expectException(InvalidMaskException::class);
        $collection->refresh($mask);
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock::mockRefresh_consecutive()
     */
    public function testMockRefresh_consecutive(): void
    {
        $maskFoo = MaskMock::initMock("Foo")->finalizeMock();
        $maskBar = MaskMock::initMock("Bar")->finalizeMock();
        $maskMoz = MaskMock::initMock("Moz")->finalizeMock();
        $collection = MaskCollectionMock::initMock()
                            ->mockRefresh_consecutive(
                                $this->exactly(3), 
                                [$maskFoo, false], [$maskBar, false], [$maskMoz, false])
                            ->finalizeMock();
        
        $this->assertNull($collection->refresh($maskFoo));
        $this->assertNull($collection->refresh($maskBar));
        $this->assertNull($collection->refresh($maskMoz));
        
        $collection = MaskCollectionMock::initMock()
                            ->mockRefresh_consecutive(
                                $this->exactly(2),
                                [$maskFoo, false], [$maskBar, true], [$maskMoz, false])
                            ->finalizeMock();
        
        $this->expectException(InvalidMaskException::class);
        $collection->refresh($maskFoo);
        $collection->refresh($maskBar);
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskCollectionMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'has' has been already mocked for this mocked mask collection");
        
        $collection = MaskCollectionMock::initMock()
                            ->mockHas($this->any(), "Foo", false)
                            ->mockHas($this->any(), "Bar", true)->finalizeMock();
    }
                    
    
}
