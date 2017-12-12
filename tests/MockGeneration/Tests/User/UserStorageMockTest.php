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

namespace ZoeTest\Component\Security\MockGeneration\Tests\User;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\User\AuthenticatedUserInterface;
use ZoeTest\Component\Security\MockGeneration\User\UserStorageMock;
use Zoe\Component\Security\Exception\User\UserNotFoundException;

/**
 * UserStorageMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserStorageMockTest extends TestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockAddUser()
     */
    public function testMockAddUser(): void
    {
        $user = UserMock::init("StoredUser", AuthenticatedUserInterface::class)->finalizeMock();
        $store = UserStorageMock::init("Foo")->mockAddUser($this->once(), "Foo", $user)->finalizeMock();
        
        $this->assertNull($store->addUser("Foo", $user));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockAddUser_consecutive()
     */
    public function testMockAddUser_consecutive(): void
    {
        $userFoo = UserMock::init("StoredUserFoo", AuthenticatedUserInterface::class)->finalizeMock();
        $userBar = UserMock::init("StoredUserBar", AuthenticatedUserInterface::class)->finalizeMock();
        $store = UserStorageMock::init("Foo")
                                    ->mockAddUser_consecutive(
                                        $this->exactly(2), 
                                        [["Foo", $userFoo], ["Bar", $userBar]])
                                ->finalizeMock();
        
        $this->assertNull($store->addUser("Foo", $userFoo));
        $this->assertNull($store->addUser("Bar", $userBar));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockGetUser()
     */
    public function testMockGetUser(): void
    {
        $user = UserMock::init("StoredUser", AuthenticatedUserInterface::class)->mockGetName($this->once(), "Foo")->finalizeMock();
        $store = UserStorageMock::init("Foo")->mockGetUser($this->once(), "Foo", $user)->finalizeMock();
        
        $user = $store->getUser("Foo");
        $this->assertInstanceOf(AuthenticatedUserInterface::class, $user);
        $this->assertSame("Foo", $user->getName());
        
        $this->expectException(UserNotFoundException::class);
        $store = UserStorageMock::init("Foo")->mockGetUser($this->once(), "Foo", null)->finalizeMock();
        $store->getUser("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockGetUser_consecutive()
     */
    public function testMockGetUser_consecutive(): void
    {
        $userFoo = UserMock::init("StoredUserFoo", AuthenticatedUserInterface::class)->mockGetName($this->once(), "Foo")->finalizeMock();
        $userBar = UserMock::init("StoredUserBar", AuthenticatedUserInterface::class)->mockGetName($this->once(), "Bar")->finalizeMock();
        $store = UserStorageMock::init("Foo")
                                    ->mockGetUser_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        $userFoo, $userBar)
                                ->finalizeMock();
        
        $userFoo = $store->getUser("Foo");
        $userBar = $store->getUser("Bar");
        $this->assertInstanceOf(AuthenticatedUserInterface::class, $userFoo);
        $this->assertSame("Foo", $userFoo->getName());
        $this->assertInstanceOf(AuthenticatedUserInterface::class, $userBar);
        $this->assertSame("Bar", $userBar->getName());
        
        $this->expectException(UserNotFoundException::class);
        $userFoo = UserMock::init("StoredUserFoo", AuthenticatedUserInterface::class)->mockGetName($this->once(), "Foo")->finalizeMock();
        $userBar = null;
        $store = UserStorageMock::init("Foo")
                                    ->mockGetUser_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        $userFoo, $userBar)
                                ->finalizeMock();
        
        $this->assertSame("Foo", $store->getUser("Foo")->getName());
        $store->getUser("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockDeleteUser()
     */
    public function testMockDeleteUser(): void
    {
        $store = UserStorageMock::init("Foo")->mockDeleteUser($this->once(), "Foo")->finalizeMock();
        
        $this->assertNull($store->deleteUser("Foo"));
        
        $this->expectException(UserNotFoundException::class);
        $store = UserStorageMock::init("Foo")->mockDeleteUser($this->once(), "Foo", true)->finalizeMock();
        $store->deleteUser("Foo");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockDeleteUser_consecutive()
     */
    public function testMockDeleteUser_consecutive(): void
    {
        $store = UserStorageMock::init("Foo")
                                    ->mockDeleteUser_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        false, false)
                                ->finalizeMock();
        
        $this->assertNull($store->deleteUser("Foo"));
        $this->assertNull($store->deleteUser("Bar"));
        
        $this->expectException(UserNotFoundException::class);
        $store = UserStorageMock::init("Foo")
                                    ->mockDeleteUser_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        false, true)
                                ->finalizeMock();
        
        $this->assertNull($store->deleteUser("Foo"));
        $store->deleteUser("Bar");
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockRefreshUser()
     */
    public function testMockRefreshUser(): void
    {
        $user = UserMock::init("RefreshedUser", AuthenticatedUserInterface::class)->finalizeMock();
        $store = UserStorageMock::init("Foo")->mockRefreshUser($this->once(), "Foo", $user)->finalizeMock();
        
        $this->assertNull($store->refreshUser("Foo", $user));
        
        $this->expectException(UserNotFoundException::class);
        $store = UserStorageMock::init("Foo")->mockRefreshUser($this->once(), "Foo", $user, true)->finalizeMock();
        
        $store->refreshUser("Foo", $user);
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockRefreshUser_consecutive()
     */
    public function testMockRefreshUser_consecutive(): void
    {
        $userFoo = UserMock::init("RefreshedUserFoo", AuthenticatedUserInterface::class)->finalizeMock();
        $userBar = UserMock::init("RefreshedUserBar", AuthenticatedUserInterface::class)->finalizeMock();
        $store = UserStorageMock::init("Foo")->mockRefreshUser_consecutive(
                                    $this->exactly(2), 
                                    [["Foo", $userFoo], ["Bar", $userBar]], 
                                    false, false)
                            ->finalizeMock();
        
        $this->assertNull($store->refreshUser("Foo", $userFoo));
        $this->assertNull($store->refreshUser("Bar", $userBar));
        
        $this->expectException(UserNotFoundException::class);
        $store = UserStorageMock::init("Foo")->mockRefreshUser_consecutive(
                                    $this->exactly(2),
                                    [["Foo", $userFoo], ["Bar", $userBar]],
                                    false, true)
                            ->finalizeMock();
            
        $this->assertNull($store->refreshUser("Foo", $userFoo));
        $store->refreshUser("Bar", $userBar);
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockIsStored()
     */
    public function testMockIsStored(): void
    {
        $store = UserStorageMock::init("Foo")->mockIsStored($this->once(), "Foo", true)->finalizeMock(); 
        $this->assertTrue($store->isStored("Foo"));
        
        $store = UserStorageMock::init("Foo")->mockIsStored($this->once(), "Foo", false)->finalizeMock();
        $this->assertFalse($store->isStored("Foo"));
    }
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserStorageMock::mockIsStored_consecutive()
     */
    public function testMockIsStore_consecutive(): void
    {
        $store = UserStorageMock::init("Foo")
                                    ->mockIsStored_consecutive(
                                        $this->exactly(2), 
                                        [["Foo"], ["Bar"]], 
                                        true, false)
                                ->finalizeMock();
        
        $this->assertTrue($store->isStored("Foo"));
        $this->assertFalse($store->isStored("Bar"));
    }
    
}
