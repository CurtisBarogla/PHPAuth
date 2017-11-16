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
use Zoe\Component\Security\Storage\UserStorageInteface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;

/**
 * Common to all UserStorageInterface testcases
 * To set more specific tests for a storage, overload a method or set it into the dedicated class
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
abstract class AbstractUserStorageTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface
     */
    public function testInterface(): void
    {
        $this->assertInstanceOf(UserStorageInteface::class, $this->getStore());
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::addUser()
     */
    public function testAddUser(): void
    {
        $store = $this->getStore();
        
        $user = $this->getMockedUser(StorableUserInterface::class, "foo", false, 1, 1);
        
        $this->assertNull($store->addUser(UserStorageInteface::STORE_USER_ID, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::getUser()
     */
    public function testGetUser(): void
    {
        $store = $this->getStore();
        
        $user = $this->getMockedUser(StorableUserInterface::class, "foo", false, 1, 1);
        
        $store->addUser(UserStorageInteface::STORE_USER_ID, $user);
        
        $getted = $store->getUser(UserStorageInteface::STORE_USER_ID);
        
        $this->assertInstanceOf(StorableUserInterface::class, $getted);
        $this->assertSame("foo", $getted->getName());
        $this->assertFalse($getted->isRoot());
        $this->assertSame(["foo" => "foo"], $getted->getRoles());
        $this->assertSame(["foo" => "bar"], $getted->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::deleteUser()
     */
    public function testDeleteUser(): void
    {
        $store = $this->getStore();
        
        $store->addUser(UserStorageInteface::STORE_USER_ID, $this->getMockedUser(StorableUserInterface::class, "foo"));
        
        $this->assertTrue($store->hasUser(UserStorageInteface::STORE_USER_ID));
        $this->assertNull($store->deleteUser(UserStorageInteface::STORE_USER_ID));
        $this->assertFalse($store->hasUser(UserStorageInteface::STORE_USER_ID));
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::refreshUser()
     */
    public function testRefreshUser(): void
    {
        $store = $this->getStore();
        
        $store->addUser(UserStorageInteface::STORE_USER_ID, $this->getMockedUser(StorableUserInterface::class, "foo"));
        
        $newUser = $this->getMockedUser(StorableUserInterface::class, "foo", true, 1, 1);
        
        $this->assertNull($store->refreshUser(UserStorageInteface::STORE_USER_ID, $newUser));
        
        $getted = $store->getUser(UserStorageInteface::STORE_USER_ID);
        
        $this->assertTrue($getted->isRoot());
        $this->assertSame(["foo" => "foo"], $getted->getRoles());
        $this->assertSame(["foo" => "bar"], $getted->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::deleteUser()
     */
    public function testHasUser(): void
    {
        $store = $this->getStore();
        
        $store->addUser(UserStorageInteface::STORE_USER_ID, $this->getMockedUser(StorableUserInterface::class, "foo"));
        
        $this->assertTrue($store->hasUser(UserStorageInteface::STORE_USER_ID));
        $this->assertFalse($store->hasUser(UserStorageInteface::STORE_USER_ID."foo"));
    }
    
                    /**_____EXCEPTIONS_____**/
   
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::getUser()
     */
    public function testExceptionGetUserWhenInvalid(): void
    {
        $identifier = UserStorageInteface::STORE_USER_ID;
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("No user found into the store for this identifier '{$identifier}'");
        
        $this->getStore()->getUser(UserStorageInteface::STORE_USER_ID);
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::deleteUser()
     */
    public function testExceptionDeleteUserWhenInvalid(): void
    {
        $identifier = UserStorageInteface::STORE_USER_ID;
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("No user found into the store for this identifier '{$identifier}'");
        
        $this->getStore()->deleteUser(UserStorageInteface::STORE_USER_ID);
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::refreshUser()
     */
    public function testExceptionRefreshUserWhenInvalid(): void
    {
        $identifier = UserStorageInteface::STORE_USER_ID;
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("No user found into the store for this identifier '{$identifier}'");
        
        $this->getStore()->refreshUser(UserStorageInteface::STORE_USER_ID, $this->getMockedUser(StorableUserInterface::class, "foo"));
    }
    
    /**
     * Get the tested UserStore
     * 
     * @return UserStorageInteface
     *   Tested UserStorageInteface implementation
     */
    abstract protected function getStore(): UserStorageInteface;
    
}
