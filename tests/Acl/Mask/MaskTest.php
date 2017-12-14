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
use Zoe\Component\Security\Acl\Mask\Mask;
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;

/**
 * Mask testcase
 * 
 * @see \Zoe\Component\Security\Acl\Mask\Mask
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask
     */
    public function testInterface(): void
    {
        $mask = new Mask("Foo");
        
        $this->assertInstanceOf(\JsonSerializable::class, $mask);
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::getIdentifier()
     */
    public function testGetIdentifier(): void
    {
        $mask = new Mask("Foo");
        
        $this->assertSame("Foo", $mask->getIdentifier());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::getValue()
     */
    public function testGetValue(): void
    {
        $mask = new Mask("Foo");
        $this->assertSame(0, $mask->getValue());
        
        $mask = new Mask("Foo", 7);
        $this->assertSame(7, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::add()
     */
    public function testAdd(): void
    {
        $mask = new Mask("Foo", 1);
        $add = MaskMock::init("MaskAdd")->mockGetValue($this->once(), 2)->finalizeMock();
        
        $this->assertNull($mask->add($add));
        $this->assertSame(3, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::sub()
     */
    public function testSub(): void
    {
        $mask = new Mask("Foo", 3);
        $sub = MaskMock::init("MaskSub")->mockGetValue($this->once(), 1)->finalizeMock();
        
        $this->assertNull($mask->sub($sub));
        $this->assertSame(2, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::rshift()
     */
    public function testRshift(): void
    {
        $mask = new Mask("Foo", 2);
        
        $this->assertNull($mask->rshift());
        $this->assertSame(1, $mask->getValue());
        
        $mask = new Mask("Foo", 4);
        
        $this->assertNull($mask->rshift(2));
        $this->assertSame(1, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::lshift()
     */
    public function testLshift(): void
    {
        $mask = new Mask("Foo", 1);
        
        $this->assertNull($mask->lshift());
        $this->assertSame(2, $mask->getValue());
        
        $mask = new Mask("Foo", 1);
        
        $this->assertNull($mask->lshift(2));
        $this->assertSame(4, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $mask = new Mask("Foo", 7);
        
        $this->assertNotFalse(\json_encode($mask));
    }
    
}
