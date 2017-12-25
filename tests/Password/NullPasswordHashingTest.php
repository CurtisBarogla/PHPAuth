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

namespace Zoe\Component\Security\Password;

use PHPUnit\Framework\TestCase;

/**
 * NullPasswordHashing testcase
 * 
 * @see \Zoe\Component\Security\Password\NullPasswordHashing
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NullPasswordHashingTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Password\NullPasswordHashing::hash()
     */
    public function testHash(): void
    {
        $hashing = new NullPasswordHashing();
        
        $this->assertSame("Foo", $hashing->hash("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Password\NullPasswordHashing::check()
     */
    public function testCheck(): void
    {
        $hashing = new NullPasswordHashing();
        
        $this->assertTrue($hashing->check("Foo", "Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Password\NullPasswordHashing::rehash()
     */
    public function testRehash(): void
    {
        $hashing = new NullPasswordHashing();
        
        $this->assertFalse($hashing->rehash("Foo"));
    }
    
}
