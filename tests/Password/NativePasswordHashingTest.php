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

namespace ZoeTest\Component\Security\Password;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\Password\NativePasswordHashing;
use Zoe\Component\Security\Password\PasswordHashingInterface;

/**
 * NativePasswordHashing testcase
 * 
 * @see \Zoe\Component\Security\Password\NativePasswordHashing
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativePasswordHashingTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\Password\NativePasswordHashing::__construct()
     */
    public function testConstructDefault(): void
    {
        $hashing = new NativePasswordHashing();
        
        $this->assertInstanceOf(PasswordHashingInterface::class, $hashing);
    }
    
    /**
     * @see \Zoe\Component\Security\Password\NativePasswordHashing::__construct()
     */
    public function testConstructBcrypt(): void
    {
        // default
        $hashing = new NativePasswordHashing(PASSWORD_BCRYPT);
        
        $this->assertInstanceOf(PasswordHashingInterface::class, $hashing);
        
        // options
        $hashing = new NativePasswordHashing(PASSWORD_BCRYPT, ["cost" => 12, "salt" => "Foo"]);
        
        $this->assertInstanceOf(PasswordHashingInterface::class, $hashing);
    }
    
    /**
     * @see \Zoe\Component\Security\Password\NativePasswordHashing::__construct()
     */
    public function testConstructArgon2(): void
    {
        // for testing purpose, will not work
        if(!\defined("PASSWORD_ARGON2I")) {
            \define("PASSWORD_ARGON2I", 3);
            \define("PASSWORD_ARGON2_DEFAULT_MEMORY_COST", 1);
            \define("PASSWORD_ARGON2_DEFAULT_TIME_COST", 2);
            \define("PASSWORD_ARGON2_DEFAULT_THREADS", 5);
        }
        
        $hashing = new NativePasswordHashing(PASSWORD_ARGON2I);
        
        $this->assertInstanceOf(PasswordHashingInterface::class, $hashing);
    }
    
    /**
     * @see \Zoe\Component\Security\Password\NativePasswordHashing::hash()
     */
    public function testHash(): void
    {
        $hashing = new NativePasswordHashing();
        
        $this->assertTrue(\is_string($hashing->hash("Foo")));
    }
    
    /**
     * @see \Zoe\Component\Security\Password\NativePasswordHashing::check()
     */
    public function testCheck(): void
    {
        $hashing = new NativePasswordHashing();
        
        $foo = $hashing->hash("Foo");
        
        $this->assertTrue($hashing->check("Foo", $foo));
    }
    
    /**
     * @see \Zoe\Component\Security\Password\NativePasswordHashing::rehash()
     */
    public function testRehash(): void
    {
        $hashing = new NativePasswordHashing();
        
        $old = $hashing->hash("Foo");
        
        $hashing = new NativePasswordHashing(null, ["cost" => 6]);
        
        $this->assertTrue($hashing->rehash($old));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Password\NativePasswordHashing::__construct()
     */
    public function testExceptionWhenAlgorithmIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Algorithm given for NativePasswordHashing is invalid");
        
        $hashing = new NativePasswordHashing(5);
    }
    
}
