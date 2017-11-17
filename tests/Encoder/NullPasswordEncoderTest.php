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
use Zoe\Component\Security\Encoder\NullPasswordEncoder;

/**
 * NullPasswordEncoder testcase
 * 
 * @see \Zoe\Component\Security\Encoder\NullPasswordEncoder
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NullPasswordEncoderTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Encoder\NullPasswordEncoder::encode()
     */
    public function testEncode(): void
    {
        $encoder = new NullPasswordEncoder();
        
        $this->assertSame("foo", $encoder->encode("foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Encoder\NullPasswordEncoder::compare()
     */
    public function testCompare(): void
    {
        $encoder = new NullPasswordEncoder();
        
        $this->assertTrue($encoder->compare("foo", "foo"));
    }
    
}
