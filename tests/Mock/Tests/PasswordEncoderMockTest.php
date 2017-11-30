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
use ZoeTest\Component\Security\Mock\PasswordEncoderMock;

/**
 * PasswordEncoderMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\PasswordEncoderMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordEncoderMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\PasswordEncoderMock::mockEncode()
     */
    public function testMockEncode(): void
    {
        $encoder = PasswordEncoderMock::initMock()->mockEncode($this->once(), "Foo", "Bar")->finalizeMock();
        
        $this->assertSame("Bar", $encoder->encode("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\PasswordEncoderMock::mockCompare()
     */
    public function testMockCompare(): void
    {
        $encoder = PasswordEncoderMock::initMock()->mockCompare($this->once(), "Foo", "Foo", true)->finalizeMock();
        
        $this->assertTrue($encoder->compare("Foo", "Foo"));
        
        $encoder = PasswordEncoderMock::initMock()->mockCompare($this->once(), "Foo", "Bar", false)->finalizeMock();
        
        $this->assertFalse($encoder->compare("Foo", "Bar"));       
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\PasswordEncoderMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'compare' has been already mocked for this mocked password encoder");
        
        $encoder = PasswordEncoderMock::initMock()
                        ->mockCompare($this->once(), "Foo", "Bar", false)
                        ->mockCompare($this->once(), "Foo", "Bar", false)
                        ->finalizeMock();
    }
    
}
