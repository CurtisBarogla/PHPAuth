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
use ZoeTest\Component\Security\Mock\UserMock;

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
        
        $user = UserMock::initMock(StorableUserInterface::class, "Foo")->finalizeMock();
        
        $this->assertNull($store->addUser(UserStorageInteface::STORE_USER_ID, $user));
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::getUser()
     */
    public function testGetUser(): void
    {
        $store = $this->getStore();
        
        $user = UserMock::initMock(StorableUserInterface::class, "Foo")->finalizeMock();

        $store->addUser(UserStorageInteface::STORE_USER_ID, $user);
        
        $getted = $store->getUser(UserStorageInteface::STORE_USER_ID);
        
        $this->assertEquals($getted, $user);
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::deleteUser()
     */
    public function testDeleteUser(): void
    {
        $store = $this->getStore();
        
        $user = UserMock::initMock(StorableUserInterface::class, "Foo");
        
        $store->addUser(UserStorageInteface::STORE_USER_ID, $user);
        
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
        
        $user1 = UserMock::initMock(StorableUserInterface::class, "Foo")->mockGetName($this->any())->finalizeMock();
        $user2 = UserMock::initMock(StorableUserInterface::class, "Bar")->mockGetName($this->once())->finalizeMock();
        
        $store->addUser(UserStorageInteface::STORE_USER_ID, $user1);

        $this->assertNull($store->refreshUser(UserStorageInteface::STORE_USER_ID, $user2));
        
        $getted = $store->getUser(UserStorageInteface::STORE_USER_ID);
        $this->assertSame("Bar", $getted->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\Storage\UserStorageInteface::deleteUser()
     */
    public function testHasUser(): void
    {
        $store = $this->getStore();
        
        $user = UserMock::initMock(StorableUserInterface::class, "Foo")->finalizeMock();
        
        $store->addUser(UserStorageInteface::STORE_USER_ID, $user);
        
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
        $user = UserMock::initMock(StorableUserInterface::class, "Foo")->finalizeMock();
        $identifier = UserStorageInteface::STORE_USER_ID;
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("No user found into the store for this identifier '{$identifier}'");
        
        $this->getStore()->refreshUser(UserStorageInteface::STORE_USER_ID, $user);
    }
    
    /**
     * Get the tested UserStore
     * 
     * @return UserStorageInteface
     *   Tested UserStorageInteface implementation
     */
    abstract protected function getStore(): UserStorageInteface;
    
}
