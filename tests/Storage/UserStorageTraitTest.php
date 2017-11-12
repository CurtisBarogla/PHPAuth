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

namespace ZoeTest\Component\Security\Storage;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Storage\UserStorageTrait;
use Zoe\Component\Security\Storage\UserStorageInteface;
use Zoe\Component\Security\Exception\LogicException;

/**
 * UserStorageTrait testcase
 * 
 * @see \Zoe\Component\Security\Storage\UserStorageTrait
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserStorageTraitTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageTrait::getStorage()
     * @see \Zoe\Component\Security\Storage\UserStorageTrait::setStorage()
     */
    public function testGetAndSetStorage(): void
    {
        $methods = $this->reflection_extractMethods(new \ReflectionClass(UserStorageInteface::class));
        
        $store = $this->getMockBuilder(UserStorageInteface::class)->setMethods($methods)->getMock();
        $trait = $this->getMockedTrait();
        $this->assertNull($trait->setStorage($store));
        $this->assertInstanceOf(UserStorageInteface::class, $trait->getStorage());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageTrait::getStorage()
     */
    public function testExceptionGetStorageOnNonSettedStorage(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("User storage is not setted");
        
        $trait = $this->getMockedTrait();
        $trait->getStorage();
    }
    
    /**
     * Get the mocked trait
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   UserStorageTrait mocked
     */
    private function getMockedTrait(): \PHPUnit_Framework_MockObject_MockObject
    {
        $mock = $this->getMockBuilder(UserStorageTrait::class)->getMockForTrait();
        
        return $mock;
    }
    
}
