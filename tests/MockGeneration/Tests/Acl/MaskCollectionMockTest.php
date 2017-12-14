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
use ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;
use Zoe\Component\Internal\GeneratorTrait;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Exception\Acl\InvalidMaskException;

/**
 * MaskCollectionMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskCollectionMockTest extends TestCase
{
        
    use GeneratorTrait;
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockGetIterator()
     */
    public function testMockGetIterator(): void
    {
        $masks = [
            "Foo"   =>  MaskMock::init("MaskFoo")->finalizeMock(),
            "Bar"   =>  MaskMock::init("MaskBar")->finalizeMock()
        ];
        $generator = $this->getGenerator($masks);
        
        $collection = MaskCollectionMock::init("Foo")->mockGetIterator($this->once(), $generator)->finalizeMock();
        
        foreach ($collection as $mask) {
            $this->assertInstanceOf(Mask::class, $mask);
        }
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockTotal()
     */
    public function testMockTotal(): void
    {
        $total = MaskMock::init("MaskTotal")->mockGetIdentifier($this->once(), "Foo")->mockGetValue($this->once(), 3)->finalizeMock();
        
        $collection = MaskCollectionMock::init("Foo")->mockTotal($this->once(), "Foo", null, $total)->finalizeMock();
        
        $this->assertSame("Foo", $collection->total("Foo")->getIdentifier());
        $this->assertSame(3, $collection->total("Foo")->getValue());
        
        $this->expectException(InvalidMaskException::class);
        $collection = MaskCollectionMock::init("Foo")->mockTotal($this->once(), null, null, null)->finalizeMock();
        
        $collection->total();
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockTotal_consecutive()
     */
    public function testMockTotal_consecutive(): void
    {
        $totalFoo = MaskMock::init("MaskTotalFoo")->mockGetIdentifier($this->once(), "Foo")->mockGetValue($this->once(), 1)->finalizeMock();
        $totalBar = MaskMock::init("MaskTotalBar")->mockGetIdentifier($this->once(), "Bar")->mockGetValue($this->once(), 3)->finalizeMock();
        
        $collection = MaskCollectionMock::init("Foo")
                                            ->mockTotal_consecutive(
                                                $this->exactly(2), 
                                                [["Foo", null], ["Bar", ["Foo", "Bar"]]], 
                                                $totalFoo, $totalBar)
                                        ->finalizeMock();
        
        $totalFoo = $collection->total("Foo");
        $totalBar = $collection->total("Bar", ["Foo", "Bar"]);
        $this->assertSame("Foo", $totalFoo->getIdentifier());
        $this->assertSame(1, $totalFoo->getValue());
        
        $this->assertSame("Bar", $totalBar->getIdentifier());
        $this->assertSame(3, $totalBar->getValue());
        
        $this->expectException(InvalidMaskException::class);
        $collection = MaskCollectionMock::init("Foo")
                                            ->mockTotal_consecutive(
                                                $this->exactly(2), 
                                                [["Foo", null], ["Bar", ["Foo", "Bar"]]], 
                                                $totalFoo, null)
                                        ->finalizeMock();
        $collection->total("Foo");
        $collection->total("Bar", ["Foo", "Bar"]);
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockAdd()
     */
    public function testMockAdd(): void
    {
        $mask = MaskMock::init("MaskAdd")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        $collection = MaskCollectionMock::init("Foo")->mockAdd($this->once(), $mask)->finalizeMock();
        
        $this->assertNull($collection->add($mask));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockAdd_consecutive()
     */
    public function testMockAdd_consecutive(): void
    {
        $masks = [
            "Foo"   =>  MaskMock::init("MaskAddFoo")->mockGetIdentifier($this->once(), "Foo")->finalizeMock(),
            "Bar"   =>  MaskMock::init("MaskAddBar")->mockGetIdentifier($this->once(), "Bar")->finalizeMock()
        ];
        
        $collection = MaskCollectionMock::init("Foo")
                                            ->mockAdd_consecutive(
                                                $this->exactly(2), 
                                                [
                                                    [$masks["Foo"]], 
                                                    [$masks["Bar"]]
                                                ])
                                        ->finalizeMock();
        
        $this->assertNull($collection->add($masks["Foo"]));
        $this->assertNull($collection->add($masks["Bar"]));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockGet()
     */
    public function testMockGet(): void
    {
        $mask = MaskMock::init("MaskReturned")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        $collection = MaskCollectionMock::init("Foo")->mockGet($this->once(), "Foo", $mask)->finalizeMock();
        
        $this->assertSame("Foo", $collection->get("Foo")->getIdentifier());
        
        $this->expectException(InvalidMaskException::class);
        $mask = null;
        $collection = MaskCollectionMock::init("Foo")->mockGet($this->once(), "Foo", $mask)->finalizeMock();
        
        $collection->get("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockGet_consecutive()
     */
    public function testMockGet_consecutive(): void
    {
        $maskFoo = MaskMock::init("MaskFooReturned")->finalizeMock();
        $maskBar = MaskMock::init("MaskBarReturned")->finalizeMock();
        
        $collection = MaskCollectionMock::init("Foo")
                                            ->mockGet_consecutive(
                                                $this->exactly(2), 
                                                [["Foo"], ["Bar"]], 
                                                $maskFoo, $maskBar)
                                        ->finalizeMock();
        
        $this->assertSame($maskFoo, $collection->get("Foo"));
        $this->assertSame($maskBar, $collection->get("Bar"));
        
        $this->expectException(InvalidMaskException::class);
        $collection = MaskCollectionMock::init("Foo")
                                            ->mockGet_consecutive(
                                                $this->exactly(2), 
                                                [["Foo"], ["Bar"]], 
                                                $maskFoo, null)
                                        ->finalizeMock();
        
        $this->assertSame($maskFoo, $collection->get("Foo"));
        $collection->get("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockHas()
     */
    public function testMockHas(): void
    {
        $collection = MaskCollectionMock::init("Foo")->mockHas($this->once(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($collection->has("Foo"));
        
        $collection = MaskCollectionMock::init("Foo")->mockHas($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($collection->has("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskCollectionMock::mockHas_consecutive()
     */
    public function testMockHas_consecutive(): void
    {
        $collection = MaskCollectionMock::init("Foo")->mockHas_consecutive($this->exactly(2), [["Foo"], ["Bar"]], true, false)->finalizeMock();
        
        $this->assertTrue($collection->has("Foo"));
        $this->assertFalse($collection->has("Bar"));
    }
    
}
