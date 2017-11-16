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
use Zoe\Component\Security\Exception\LogicException;
use Zoe\Component\Security\Storage\UserStorageTrait;
use Zoe\Component\Security\Storage\UserStorageInteface;

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
     */
    public function testGetStorage(): void
    {
        $trait = $this->getMockedTrait();
        $store = $this
            ->getMockBuilder(UserStorageInteface::class)
            ->setMethods($this->reflection_extractMethods(new \ReflectionClass(UserStorageInteface::class)))
            ->getMock();
        $trait->setStorage($store);
        
        $this->assertInstanceOf(UserStorageInteface::class, $trait->getStorage());
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageTrait::setStorage()
     */
    public function testSetStorage(): void
    {
        $trait = $this->getMockedTrait();
        $store = $this
                    ->getMockBuilder(UserStorageInteface::class)
                    ->setMethods($this->reflection_extractMethods(new \ReflectionClass(UserStorageInteface::class)))
                    ->getMock();
        
        $this->assertNull($trait->setStorage($store));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageTrait::getStorage()
     */
    public function testExceptionWhenStorageNotSetted(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("User storage is not setted");
        
        $this->getMockedTrait()->getStorage();
    }
    
    /**
     * Get a mock of UserStorageTrait
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked trait
     */
    private function getMockedTrait(): \PHPUnit_Framework_MockObject_MockObject
    {
        $trait = $this->getMockForTrait(UserStorageTrait::class);
        
        return $trait;
    }
    
}
