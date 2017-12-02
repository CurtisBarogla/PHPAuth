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

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Acl\Mask\MaskCollection;
use Zoe\Component\Security\Acl\Mask\Mask;
use Zoe\Component\Security\Exception\InvalidMaskException;
use ZoeTest\Component\Security\Mock\MaskMock;

/**
 * MaskCollection testcase
 * 
 * @see \Zoe\Component\Security\Acl\Mask\MaskCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskCollectionTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::getIterator()
     */
    public function testGetIterator(): void
    {
        $collection = new MaskCollection("Foo");
        $mask1 = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->finalizeMock();
        $mask2 = MaskMock::initMock("Bar")->mockGetIdentifier($this->once())->finalizeMock();
        $collection->add($mask1);
        $collection->add($mask2);
        
        $expected = $this->getGenerator(["Foo" => $mask1, "Bar" => $mask2]);
        
        $this->assertTrue($this->assertGeneratorEquals($expected, $collection->getIterator()));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::total()
     */
    public function testTotal(): void
    {
        $mask1 = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->mockGetValue($this->any(), 1)->finalizeMock();
        $mask2 = MaskMock::initMock("Bar")->mockGetIdentifier($this->once())->mockGetValue($this->any(), 2)->finalizeMock();
        $mask3 = MaskMock::initMock("Moz")->mockGetIdentifier($this->once())->mockGetValue($this->any(), 4)->finalizeMock();
        $collection = new MaskCollection("foo");
        $collection->add($mask1);
        $collection->add($mask2);
        $collection->add($mask3);

        $this->assertEquals(7, $collection->total("total")->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::add()
     */
    public function testAdd(): void
    {
        $mask = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->finalizeMock();
        $collection = new MaskCollection("Foo");
        
        $this->assertNull($collection->add($mask));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::get()
     */
    public function testGet(): void
    {
        $mask = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->mockGetValue($this->once(), 1)->finalizeMock();
        $collection = new MaskCollection("foo");
        $collection->add($mask);
        
        $this->assertSame(1, $collection->get("Foo")->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::has()
     */
    public function testHas(): void
    {
        $mask = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->finalizeMock();
        $collection = new MaskCollection("foo");
        
        $this->assertFalse($collection->has("Foo"));
        
        $collection->add($mask);
        
        $this->assertTrue($collection->has("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::refresh()
     */
    public function testRefresh(): void
    {
        $collection = new MaskCollection("foo");
        $old = MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->mockGetValue($this->once(), 1)->finalizeMock();
        $new = MaskMock::initMock("Foo")->mockGetIdentifier($this->exactly(2))->mockGetValue($this->once(), 4)->finalizeMock();
        $collection->add($old);
        
        $this->assertSame(1, $collection->get("Foo")->getValue());
        $this->assertNull($collection->refresh($new));
        $this->assertSame(4, $collection->get("Foo")->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $mask = new Mask("foo", 0x0000);
        $mask2 = new Mask("bar", 0x0001);
        
        $collection = new MaskCollection("foo");
        $collection->add($mask);
        $collection->add($mask2);
        
        $this->assertNotFalse(\json_encode($collection));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::count()
     */
    public function testCount(): void
    {
        $collection = new MaskCollection("foo");
        
        $collection->add(MaskMock::initMock("Foo")->mockGetIdentifier($this->once())->finalizeMock());
        $collection->add(MaskMock::initMock("Bar")->mockGetIdentifier($this->once())->finalizeMock());
        
        $this->assertSame(2, \count($collection));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::get()
     */
    public function testExceptionWhenGettingNonRegisteredMask(): void
    {
        $this->expectException(InvalidMaskException::class);
        $this->expectExceptionMessage("No mask 'foo' registered into 'bar'collection");
        
        $collection = new MaskCollection("bar");
        $collection->get("foo");
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::refresh()
     */
    public function testExceptionOnRefreshNotRegisteredMask(): void
    {
        $this->expectException(InvalidMaskException::class);
        $this->expectExceptionMessage("Cannot refresh this mask 'Foo' into collection 'bar'. It does not correspond to an existing one");
        
        $collection = new MaskCollection("bar");
        $collection->refresh(MaskMock::initMock("Foo")->mockGetIdentifier($this->exactly(2))->finalizeMock());
    }
    
}
