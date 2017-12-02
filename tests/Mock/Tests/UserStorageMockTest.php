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

namespace ZoeTest\Component\Security\Mock\Tests;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\UserStorageMock;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;

/**
 * UserStorageMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\UserStorageMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserStorageMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserStorageMock::mockAddUser()
     */
    public function testMockAddUser(): void
    {
        $user = UserMock::initMock(StorableUserInterface::class, "Foo")->finalizeMock();
        $store = UserStorageMock::initMock()->mockAddUser($this->any(), "Foo", $user)->finalizeMock();
        
        $this->assertNull($store->addUser("Foo", $user));
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserStorageMock::mockGetUser()
     */
    public function testMockGetUser(): void
    {
        $user = UserMock::initMock(StorableUserInterface::class, "Foo")->mockGetName($this->once())->finalizeMock();
        $store = UserStorageMock::initMock()->mockGetUser($this->once(), "Foo", $user)->finalizeMock();
        
        $this->assertInstanceOf(StorableUserInterface::class, $user);
        $this->assertSame("Foo", $store->getUser("Foo")->getName());
        
        $user = null;
        $store = UserStorageMock::initMock()->mockGetUser($this->once(), "Foo", $user)->finalizeMock();
        $this->expectException(UserNotFoundException::class);
        $store->getUser("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserStorageMock::mockDeleteUser()
     */
    public function testMockDeleteUser(): void
    {
        $store = UserStorageMock::initMock()->mockDeleteUser($this->once(), "Foo", false)->finalizeMock();
        $this->assertNull($store->deleteUser("Foo"));
        
        $store = UserStorageMock::initMock()->mockDeleteUser($this->once(), "Foo", true)->finalizeMock();
        $this->expectException(UserNotFoundException::class);
        $store->deleteUser("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserStorageMock::mockRefreshUser()
     */
    public function testMockRefreshUser(): void
    {   
        $user = UserMock::initMock(StorableUserInterface::class, "Foo")->finalizeMock();
        $store = UserStorageMock::initMock()->mockRefreshUser($this->once(), "Foo", $user)->finalizeMock();
        
        $this->assertNull($store->refreshUser("Foo", $user));
        
        $user = null;
        $store = UserStorageMock::initMock()->mockRefreshUser($this->once(), "Foo", $user)->finalizeMock();
        $this->expectException(UserNotFoundException::class);
        $store->refreshUser("Foo", UserMock::initMock(StorableUserInterface::class, "Foo")->finalizeMock());
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserStorageMock::mockHasUser()
     */
    public function testMockHasUser(): void
    {
        $store = UserStorageMock::initMock()->mockHasUser($this->once(), "Foo", true)->finalizeMock();
        
        $this->assertTrue($store->hasUser("Foo"));
        
        $store = UserStorageMock::initMock()->mockHasUser($this->once(), "Foo", false)->finalizeMock();
        
        $this->assertFalse($store->hasUser("Foo"));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserStorageMock
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'hasUser' for thos mocked user storage has been already mocked");
        
        $store = UserStorageMock::initMock()->mockHasUser($this->any(), "Foo", false)->mockHasUser($this->any(), "Foo", false)->finalizeMock();
    }
    
}
