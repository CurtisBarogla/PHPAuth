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

namespace ZoeTest\Component\Security\MockGeneration\Tests\Password;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\Password\PasswordHashingMock;

/**
 * PasswordHashingMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\Password\PasswordHashingMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordHashingMockTest extends TestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Password\PasswordHashingMock::mockHash()
     */
    public function testMockHash(): void
    {
        $hashing = PasswordHashingMock::init("Foo")->mockHash($this->once(), "Foo", null, "Foo")->finalizeMock();
        
        $this->assertSame("Foo", $hashing->hash("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Password\PasswordHashingMock::mockCheck()
     */
    public function testMockCheck(): void
    {
        $hashing = PasswordHashingMock::init("Foo")->mockCheck($this->once(), "Foo", "Foo", null, true)->finalizeMock();
        
        $this->assertTrue($hashing->check("Foo", "Foo"));
        
        $hashing = PasswordHashingMock::init("Foo")->mockCheck($this->once(), "Foo", "Bar", null, false)->finalizeMock();
        
        $this->assertFalse($hashing->check("Foo", "Bar"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\Password\PasswordHashingMock::mockRehash()
     */
    public function testMockRehash(): void
    {
        $hashing = PasswordHashingMock::init("Foo")->mockRehash($this->once(), "Foo", null, true)->finalizeMock();
        
        $this->assertTrue($hashing->rehash("Foo"));
        
        $hashing = PasswordHashingMock::init("Foo")->mockRehash($this->once(), "Foo", null, false)->finalizeMock();
        
        $this->assertFalse($hashing->rehash("Foo"));
        
    }
    
}
