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

namespace ZoeTest\Component\Security\Encoder;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Encoder\NativePasswordEncoder;
use Zoe\Component\Security\Encoder\PasswordEncoderInterface;
use Zoe\Component\Security\Exception\InvalidArgumentException;

/**
 * NativePasswordEncoder testcase
 * 
 * @see \Zoe\Component\Security\Encoder\NativePasswordEncoder
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativePasswordEncoderTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Encoder\NativePasswordEncoder
     */
    public function testInterface(): void
    {
        $encoder = new NativePasswordEncoder();
        $this->assertInstanceOf(PasswordEncoderInterface::class, $encoder);
    }
    
    /**
     * @see \Zoe\Component\Security\Encoder\NativePasswordEncoder::encode()
     */
    public function testEncode(): void
    {
        $encoder = new NativePasswordEncoder(11, PASSWORD_DEFAULT);

        $encoded = $encoder->encode("foo");
        $this->assertTrue(\is_string($encoded));
        $info = \password_get_info($encoded);
        $this->assertSame(PASSWORD_DEFAULT, $info["algo"]);
        $this->assertSame(11, $info["options"]["cost"]);
    }
    
    /**
     * @see \Zoe\Component\Security\Encoder\NativePasswordEncoder::compare()
     */
    public function testCompare(): void
    {
        $encoder = new NativePasswordEncoder();
        $encoded = $encoder->encode("foo");
        
        $this->assertTrue($encoder->compare("foo", $encoded));
        $this->assertFalse($encoder->compare("bar", $encoded));
    }
    
    /**
     * @see \Zoe\Component\Security\Encoder\NativePasswordEncoder::getBestCost()
     */
    public function testGetCost(): void
    {
        // change it if fail
        $expected = 10;
        
        $this->assertSame($expected, NativePasswordEncoder::getBestCost());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Encoder\NativePasswordEncoder::__construct()
     */
    public function testExceptionWhenInvalidAlgorithmIsGiven(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Given algorithm is invalid");
        
        $encoder = new NativePasswordEncoder(10, 4);
    }
    
    /**
     * @see \Zoe\Component\Security\Encoder\NativePasswordEncoder::__construct()
     */
    public function testExceptionWhenInvalidCostIsGiven(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cost MUST be > 2. '2' given");
        
        $encoder = new NativePasswordEncoder(2);
    }
    
}
