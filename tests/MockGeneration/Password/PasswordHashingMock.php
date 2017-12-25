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

namespace ZoeTest\Component\Security\MockGeneration\Password;

use ZoeTest\Component\Security\MockGeneration\MockGeneration;
use Zoe\Component\Security\Password\PasswordHashingInterface;
use \PHPUnit_Framework_MockObject_Matcher_Invocation as MethodCount;

/**
 * Responsible to mock password hashing
 *
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordHashingMock extends MockGeneration
{
    
    /**
     * Initialize a new password hashing mocked generation
     *
     * @param string $mockId
     *   Mock id
     *
     * @return PasswordHashingMock
     *   New password hashing mock generation
     */
    public static function init(string $mockId): PasswordHashingMock
    {
        return new PasswordHashingMock($mockId, PasswordHashingInterface::class);
    }
    
    /**
     * Finalize the mocked password hashing
     *
     * @return PasswordHashingInterface
     *   Mocked password hashing
     */
    public function finalizeMock(): PasswordHashingInterface
    {
        return $this->mock;
    }
    
    /**
     * Mock hash()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $raw
     *   Password to hash
     * @param string|null $salt
     *   Salt to apply or null
     * @param string $hashed
     *   Hash returned
     *
     * @return self
     *   Fluent
     */
    public function mockHash(MethodCount $count, string $raw, ?string $salt, string $hashed): self
    {
        $mock = function(string $method) use ($raw, $salt, $hashed, $count): void {
            $this->mock->expects($count)->method("hash")->with($raw, $salt)->will($this->returnValue($hashed)); 
        };
        
        return $this->executeMock("hash", $mock);
    }
    
    /**
     * Mock check()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $raw
     *   Clear password
     * @param string $hashed
     *   Hashed password given to compare
     * @param string|null $salt
     *   Salt to apply or null
     * @param bool $result
     *   Comparaison result
     *
     * @return self
     *   Fluent
     */
    public function mockCheck(MethodCount $count, string $raw, string $hashed, ?string $salt, bool $result): self
    {
        $mock = function(string $method) use ($raw, $hashed, $salt, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($raw, $hashed, $salt)->will($this->returnValue($result));
        };
        
        return $this->executeMock("check", $mock);
    }
    
    /**
     * Mock rehash()
     *
     * @param MethodCount $count
     *   Called count
     * @param string $password
     *   Hashed password given
     * @param string|null $salt
     *   Salt to apply or null
     * @param bool $result
     *   Result returned
     *
     * @return self
     *   Fluent
     */
    public function mockRehash(MethodCount $count, string $password, ?string $salt, bool $result): self
    {
        $mock = function(string $method) use ($password, $salt, $result, $count): void {
            $this->mock->expects($count)->method($method)->with($password, $salt)->will($this->returnValue($result)); 
        };
        
        return $this->executeMock("rehash", $mock);
    }
    
}
