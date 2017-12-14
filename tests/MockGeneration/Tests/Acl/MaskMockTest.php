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
use ZoeTest\Component\Security\MockGeneration\Acl\MaskMock;

/**
 * MaskMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskMockTest extends TestCase
{

    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockGetIdentifier()
     */
    public function testMockGetIdentifier(): void
    {
        $mask = MaskMock::init("Foo")->mockGetIdentifier($this->once(), "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $mask->getIdentifier());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockGetValue()
     */
    public function testMockGetValue(): void
    {
        $mask = MaskMock::init("Foo")->mockGetValue($this->once(), 1)->finalizeMock();
        
        $this->assertSame(1, $mask->getValue());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockGetValue_consecutive()
     */
    public function testMockGetValue_consecutive(): void
    {
        $mask = MaskMock::init("Foo")->mockGetValue_consecutive($this->exactly(2), 1, 3)->finalizeMock();
        
        $this->assertSame(1, $mask->getValue());
        $this->assertSame(3, $mask->getValue());
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockAdd()
     */
    public function testMockAdd(): void
    {
        $add = MaskMock::init("MaskAdd")->mockGetValue($this->once(), 2)->finalizeMock();
        $mask = MaskMock::init("Foo")->mockAdd($this->once(), $add)->finalizeMock();
        
        $this->assertNull($mask->add($add));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockAdd_consecutive()
     */
    public function testMockAdd_consecutive(): void
    {
        $mask1 = MaskMock::init("MaskSub1")->mockGetValue($this->once(), 2)->finalizeMock();
        $mask2 = MaskMock::init("MaskSub2")->mockGetValue($this->once(), 3)->finalizeMock();
        
        $mask = MaskMock::init("Foo")->mockAdd_consecutive($this->exactly(2), [[$mask1], [$mask2]])->finalizeMock();
        
        $this->assertNull($mask->add($mask1));
        $this->assertNull($mask->add($mask2));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockSub()
     */
    public function testMockSub(): void
    {
        $sub = MaskMock::init("MaskSub")->mockGetValue($this->once(), 2)->finalizeMock();
        $mask = MaskMock::init("Foo")->mockSub($this->once(), $sub)->finalizeMock();
        
        $this->assertNull($mask->sub($sub));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockSub_consecutive()
     */
    public function testMockSub_consecutive(): void
    {
        $mask1 = MaskMock::init("MaskSub1")->mockGetValue($this->once(), 2)->finalizeMock();
        $mask2 = MaskMock::init("MaskSub2")->mockGetValue($this->once(), 3)->finalizeMock();
        
        $mask = MaskMock::init("Foo")->mockSub_consecutive($this->exactly(2), [[$mask1], [$mask2]])->finalizeMock();
        
        $this->assertNull($mask->sub($mask1));
        $this->assertNull($mask->sub($mask2));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockRshift()
     */
    public function testMockRshift(): void
    {
        $mask = MaskMock::init("Foo")->mockRshift($this->once(), 2)->finalizeMock();
        
        $this->assertNull($mask->rshift(2));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockRshift_consecutive()
     */
    public function testMockRshift_consecutive(): void
    {
        $mask = MaskMock::init("Foo")->mockRshift_consecutive($this->exactly(2), [[2], [3]])->finalizeMock();
        
        $this->assertNull($mask->rshift(2));
        $this->assertNull($mask->rshift(3));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockLshift()
     */
    public function testMockLshift(): void
    {
        $mask = MaskMock::init("Foo")->mockLshift($this->once(), 2)->finalizeMock();
        
        $this->assertNull($mask->lshift(2));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Acl\MaskMock::mockLshift_consecutive()
     */
    public function testMockLshift_consecutive(): void
    {
        $mask = MaskMock::init("Foo")->mockLshift_consecutive($this->exactly(2), [[2], [3]])->finalizeMock();
        
        $this->assertNull($mask->lshift(2));
        $this->assertNull($mask->lshift(3));
    }
    
}
