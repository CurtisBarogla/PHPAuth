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
        $mask1 = $this->getMockedMask("foo", 0x0000);
        $mask2 = $this->getMockedMask("bar", 0x0001);
        $collection->add($mask1);
        $collection->add($mask2);
        
        $expected = $this->getGenerator(["foo" => $mask1, "bar" => $mask2]);
        
        $this->assertTrue($this->assertGeneratorEquals($expected, $collection->getIterator()));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::total()
     */
    public function testTotal(): void
    {
        $mask1 = $this->getMockedMask("foo", 0x0001);
        $mask2 = $this->getMockedMask("bar", 0x0002);
        $mask3 = $this->getMockedMask("moz", 0x0004);
        $collection = new MaskCollection("foo");
        $collection->add($mask1);
        $collection->add($mask2);
        $collection->add($mask3);
        
        $expected = new Mask("total", 0x0007);
        
        $this->assertEquals($expected, $collection->total("total"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::add()
     */
    public function testAdd(): void
    {
        $mask = $this->getMockedMask("foo", 0);
        $collection = new MaskCollection("foo");
        
        $this->assertNull($collection->add($mask));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::get()
     */
    public function testGet(): void
    {
        $mask = $this->getMockedMask("foo", 0);
        $collection = new MaskCollection("foo");
        $collection->add($mask);
        
        $this->assertSame($mask, $collection->get("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::has()
     */
    public function testHas(): void
    {
        $mask = $this->getMockedMask("foo", 0);
        $collection = new MaskCollection("foo");
        
        $this->assertFalse($collection->has("foo"));
        
        $collection->add($mask);
        
        $this->assertTrue($collection->has("foo"));
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
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::createCollectionFromJson()
     */
    public function testCreateCollectionFromJson(): void
    {
        $collection = new MaskCollection("foo");
        
        $collection->add(new Mask("foo", 0x0001));
        $collection->add(new Mask("bar", 0x0002));
        $collection->add(new Mask("moz", 0x0004));
        
        $json = \json_encode($collection);
        
        $this->assertEquals($collection, MaskCollection::createCollectionFromJson($json));
        
        $json = \json_decode($json, true);
        
        $this->assertEquals($collection, MaskCollection::createCollectionFromJson($json));
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\MaskCollection::count()
     */
    public function testCount(): void
    {
        $collection = new MaskCollection("foo");
        
        $collection->add($this->getMockedMask("foo", 0));
        $collection->add($this->getMockedMask("bar", 1));
        
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
    
}
