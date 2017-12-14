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

namespace ZoeTest\Component\Security\Acl\Mask;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Internal\GeneratorTrait;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Exception\Acl\InvalidMaskException;

/**
 * MaskCollection testcase
 * 
 * @see \Zoe\Component\Security\Acl\Mask\MaskCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskCollectionTest extends TestCase
{
    
    use GeneratorTrait;
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection
     */
    public function testInterface(): void
    {
        $collection = new MaskCollection("Foo");
        
        $this->assertInstanceOf(\JsonSerializable::class, $collection);
        $this->assertInstanceOf(\Countable::class, $collection);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::getIterator()
     */
    public function testGetIterator(): void
    {
        $masks = [
            "Foo"   =>  MaskMock::init("FooMask")->mockGetIdentifier($this->once(), "Foo")->finalizeMock(),
            "Bar"   =>  MaskMock::init("BarMask")->mockGetIdentifier($this->once(), "Bar")->finalizeMock(),
        ];
        
        $expected = $this->getGenerator($masks);
        
        $collection = new MaskCollection("Foo");
        $collection->add($masks["Foo"]);
        $collection->add($masks["Bar"]);
        
        $this->assertTrue($this->assertGeneratorEquals($expected, $collection->getIterator()));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::total()
     */
    public function testTotal(): void
    {
        $masks = [
            "Foo"   =>  MaskMock::init("FooMask")->mockGetIdentifier($this->once(), "Foo")->mockGetValue($this->exactly(3), 1)->finalizeMock(),
            "Bar"   =>  MaskMock::init("FooMask")->mockGetIdentifier($this->once(), "Bar")->mockGetValue($this->exactly(3), 2)->finalizeMock(),
            "Moz"   =>  MaskMock::init("FooMask")->mockGetIdentifier($this->once(), "Moz")->mockGetValue($this->exactly(2), 4)->finalizeMock()
        ];
        
        $collection = new MaskCollection("Foo");
        $collection->add($masks["Foo"]);
        $collection->add($masks["Bar"]);
        $collection->add($masks["Moz"]);
        
        $total = $collection->total("Foo");
        $this->assertSame("Foo", $total->getIdentifier());
        
        // total collection
        $total = $collection->total();
        $this->assertSame("TOTAL_Foo", $total->getIdentifier());
        $this->assertSame(7, $total->getValue());
        
        // defined
        $total = $collection->total(null, ["Foo", "Bar"]);
        $this->assertSame(3, $total->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::add()
     */
    public function testAdd(): void
    {
        $mask = MaskMock::init("MaskAdd")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        $collection = new MaskCollection("Foo");
        
        $this->assertNull($collection->add($mask));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::get()
     */
    public function testGet(): void
    {
        $mask = MaskMock::init("MaskAdd")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        $collection = new MaskCollection("Foo");
        
        $collection->add($mask);
        
        $this->assertSame($mask, $collection->get("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::has()
     */
    public function testHas(): void
    {
        $collection = new MaskCollection("Foo");
        
        $this->assertFalse($collection->has("Foo"));
        
        $mask = MaskMock::init("MaskAdd")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        
        $collection->add($mask);
        
        $this->assertTrue($collection->has("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $collection = new MaskCollection("Foo");
        $collection->add(new Mask("Foo"), 1);
        $collection->add(new Mask("Bar"), 2);
        
        $this->assertNotFalse(\json_encode($collection));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::count()
     */
    public function testCount(): void
    {
        $maskFoo = MaskMock::init("MaskFooAdd")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        $maskBar = MaskMock::init("MaskBarAdd")->mockGetIdentifier($this->once(), "Bar")->finalizeMock();
        
        $collection = new MaskCollection("Foo");
        $this->assertSame(0, \count($collection));
        
        $collection->add($maskFoo);
        $collection->add($maskBar);
        
        $this->assertSame(2, \count($collection));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::restoreFromJson()
     */
    public function testRestoreFromJson(): void
    {
        $collection = new MaskCollection("Foo");
        $collection->add(new Mask("Foo"), 1);
        $collection->add(new Mask("Bar"), 2);
        
        $json = \json_encode($collection);
        
        // from string
        $this->assertEquals($collection, MaskCollection::restoreFromJson($json));
        
        // from array
        $json = \json_decode($json, true);
        $this->assertEquals($collection, MaskCollection::restoreFromJson($json));
    }
    
    
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::total()
     */
    public function testExceptionTotalWhenAMaskIsNotRegistered(): void
    {
        $this->expectException(InvalidMaskException::class);
        $this->expectExceptionMessage("This mask 'Foo' into collection 'Bar' is not registered");
        
        $collection = new MaskCollection("Bar");
        $collection->total(null, ["Foo"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::get()
     */
    public function testExceptionGetWhenMaskIsNotRegistered(): void
    {
        $this->expectException(InvalidMaskException::class);
        $this->expectExceptionMessage("This mask 'Foo' into collection 'Bar' is not registered");
        
        $collection = new MaskCollection("Bar");
        $collection->get("Foo");
    }
    
}
