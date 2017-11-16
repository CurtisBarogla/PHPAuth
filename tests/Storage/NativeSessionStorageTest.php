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

use Zoe\Component\Security\Storage\NativeSessionStorage;
use Zoe\Component\Security\Storage\UserStorageInteface;
use Zoe\Component\Security\Exception\LogicException;
                
/**
 * NativeSessionStorage testcase
 * 
 * @see \Zoe\Component\Security\Storage\NativeSessionStorage
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeSessionStorageTest extends AbstractUserStorageTest
{
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Storage\AbstractUserStorageTest::testDeleteUser()
     */
    public function testDeleteUser(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Cannot delete user from this storage as php is responsible to expire invalid sessions");
        
        $this->getStore()->deleteUser(UserStorageInteface::STORE_USER_ID);
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Storage\AbstractUserStorageTest::testExceptionDeleteUserWhenInvalid()
     */
    public function testExceptionDeleteUserWhenInvalid(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Cannot delete user from this storage as php is responsible to expire invalid sessions");
        
        $this->getStore()->deleteUser(UserStorageInteface::STORE_USER_ID);
    }
    
    /**
     * {@inheritDoc}
     * @see \ZoeTest\Component\Security\Storage\AbstractUserStorageTest::getStore()
     */
    protected function getStore(): UserStorageInteface
    {
        $store = new NativeSessionStorage(false);
        $reflection = new \ReflectionClass($store);
        $this->reflection_injectNewValueIntoProperty($store, $reflection, "session", []);
        
        return $store;
    }
    
}
