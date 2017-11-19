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
use Zoe\Component\Security\Acl\Mask\Mask;

/**
 * Mask testcase
 * 
 * @see \Zoe\Component\Security\Acl\Mask\Mask
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::getIdentifier()
     */
    public function testGetIdentifier(): void
    {
        $mask = new Mask("Foo", 0b0000);
        
        $this->assertSame("Foo", $mask->getIdentifier());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::getValue()
     */
    public function testGetValue(): void
    {
        $mask = new Mask("Foo", 0b0000);
        
        $this->assertSame(0, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::add()
     */
    public function testAdd(): void
    {
        $mask = new Mask("Foo", 0b0000);
        $mask2 = new Mask("Bar", 0b0010);
        
        $this->assertNull($mask->add($mask2));
        $this->assertSame(2, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::sub()
     */
    public function testSub(): void
    {
        $mask = new Mask("Foo", 0b00011);
        $mask2 = new Mask("Bar", 0b0001);
        
        $this->assertNull($mask->sub($mask2));
        $this->assertSame(2, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::left()
     */
    public function testLeft(): void
    {
        $mask = new Mask("Foo", 0b0001);
        
        $this->assertInstanceOf(Mask::class, $mask->left()->left());
        $this->assertSame(4, $mask->getValue());
        
        $mask = new Mask("Foo", 0b0001);
        
        $this->assertInstanceOf(Mask::class, $mask->left(2)->left(2));
        $this->assertSame(16, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::right()
     */
    public function testRight(): void
    {
        $mask = new Mask("Foo", 0b0010);
        
        $this->assertInstanceOf(Mask::class, $mask->right());
        $this->assertSame(1, $mask->getValue());
        
        $mask = new Mask("Foo", 0b1000);
        $this->assertInstanceOf(Mask::class, $mask->right(2)->right());
        $this->assertSame(1, $mask->getValue());
    }
    
    /**
     * @see \Zoe\Component\Security\Acl\Mask\Mask::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $mask = new Mask("Foo", 0b0001);
        $json = \json_encode($mask);
        $this->assertSame(["identifier" => "Foo", "value" => 1], \json_decode($json, true));
    }
    
}
