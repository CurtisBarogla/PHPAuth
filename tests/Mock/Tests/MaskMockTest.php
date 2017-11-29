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
use ZoeTest\Component\Security\Mock\MaskMock;
use Zoe\Component\Security\Acl\Mask\Mask;

/**
 * MaskMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\MaskMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class MaskMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockGetIdentifier()
     */
    public function testMockGetIdentifier(): void
    {
        $mask = MaskMock::initMock("Foo")->mockGetIdentifier($this->any())->finalizeMock();
        
        $this->assertSame("Foo", $mask->getIdentifier());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockGetValue()
     */
    public function testMockGetValue(): void
    {
        $mask = MaskMock::initMock("Foo")->mockGetValue($this->any(), 0)->finalizeMock();
        
        $this->assertSame(0, $mask->getValue());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockGetValue_consecutive()
     */
    public function testMockGetValue_consecutive(): void
    {
        $mask = MaskMock::initMock("Foo")->mockGetValue_consecutive($this->exactly(3), 1, 2, 4)->finalizeMock();
        
        $this->assertSame(1, $mask->getValue());
        $this->assertSame(2, $mask->getValue());
        $this->assertSame(4, $mask->getValue());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockAdd()
     */
    public function testMockAdd(): void
    {
        $maskToAdd = MaskMock::initMock("Foo")->finalizeMock();
        $mask = MaskMock::initMock("Foo")->mockAdd($this->any(), $maskToAdd)->finalizeMock();
        
        $this->assertNull($mask->add($maskToAdd));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockAdd_consecutive()
     */
    public function testMockAdd_consecutive(): void
    {
        $maskToAdd1 = MaskMock::initMock("Foo")->finalizeMock();
        $maskToAdd2 = MaskMock::initMock("Bar")->finalizeMock();
        $maskToAdd3 = MaskMock::initMock("Moz")->finalizeMock();
        $mask = MaskMock::initMock("Foo")->mockAdd_consecutive($this->exactly(3), $maskToAdd1, $maskToAdd2, $maskToAdd3)->finalizeMock();
        
        $this->assertNull($mask->add($maskToAdd1));
        $this->assertNull($mask->add($maskToAdd2));
        $this->assertNull($mask->add($maskToAdd3));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockSub()
     */
    public function testMockSub(): void
    {
        $maskToSub = MaskMock::initMock("Foo")->finalizeMock();
        $mask = MaskMock::initMock("Foo")->mockSub($this->any(), $maskToSub)->finalizeMock();
        
        $this->assertNull($mask->sub($maskToSub));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockSub_consecutive()
     */
    public function testMockSub_consecutive(): void
    {
        $maskToSub1 = MaskMock::initMock("Foo")->finalizeMock();
        $maskToSub2 = MaskMock::initMock("Bar")->finalizeMock();
        $maskToSub3 = MaskMock::initMock("Moz")->finalizeMock();
        $mask = MaskMock::initMock("Foo")->mockSub_consecutive($this->exactly(3), $maskToSub1, $maskToSub2, $maskToSub3)->finalizeMock();
        
        $this->assertNull($mask->sub($maskToSub1));
        $this->assertNull($mask->sub($maskToSub2));
        $this->assertNull($mask->sub($maskToSub3));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockLeft()
     */
    public function testMockLeft(): void
    {
        $mask = MaskMock::initMock("Foo")->mockLeft($this->any(), 2)->finalizeMock();
        
        $this->assertInstanceOf(Mask::class, $mask->left(2));
        
        $mask = MaskMock::initMock("Foo")->mockLeft($this->any(), null)->finalizeMock();
        
        $this->assertInstanceOf(Mask::class, $mask->left());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockLeft_consecutive()
     */
    public function testMockLeft_consecutive(): void
    {
        $mask = MaskMock::initMock("Foo")->mockLeft_consecutive($this->exactly(3), null, 2, 3)->finalizeMock();
        
        $this->assertInstanceOf(Mask::class, $mask->left());
        $this->assertInstanceOf(Mask::class, $mask->left(2));
        $this->assertInstanceOf(Mask::class, $mask->left(3));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockRight()
     */
    public function testMockRight(): void
    {
        $mask = MaskMock::initMock("Foo")->mockRight($this->any(), 2)->finalizeMock();
        
        $this->assertInstanceOf(Mask::class, $mask->right(2));
        
        $mask = MaskMock::initMock("Foo")->mockRight($this->any(), null)->finalizeMock();
        
        $this->assertInstanceOf(Mask::class, $mask->right());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock::mockRight_consecutive()
     */
    public function testMockRight_consecutive(): void
    {
        $mask = MaskMock::initMock("Foo")->mockRight_consecutive($this->exactly(3), null, 2, 3)->finalizeMock();
        
        $this->assertInstanceOf(Mask::class, $mask->right());
        $this->assertInstanceOf(Mask::class, $mask->right(2));
        $this->assertInstanceOf(Mask::class, $mask->right(3));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\MaskMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'getIdentifier' for mocked mask 'Foo' has been already mocked");
        
        $mask = MaskMock::initMock("Foo")->mockGetIdentifier($this->any())->mockGetIdentifier($this->any())->finalizeMock();
    }
    
}
