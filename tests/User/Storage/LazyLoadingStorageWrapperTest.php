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

namespace ZoeTest\Component\Security\User\Storage;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use ZoeTest\Component\Security\MockGeneration\User\UserStorageMock;
use Zoe\Component\Security\User\AuthenticatedUserInterface;
use Zoe\Component\Security\User\Storage\LazyLoadingStorageWrapper;
use Zoe\Component\Security\User\Storage\UserStorageInterface;

/**
 * LazyLoadingStorageWrapper testcase
 * 
 * @see \Zoe\Component\Security\User\Storage\LazyLoadingStorageWrapper
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class LazyLoadingStorageWrapperTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\Storage\LazyLoadingStorageWrapper::addUser()
     */
    public function testAddUser(): void
    {
        $user = null;
        $wrapped = $this->getWrappedStore($user);
        $wrapper = new LazyLoadingStorageWrapper($wrapped);
        
        $this->assertNull($wrapper->addUser("Foo", $user));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\LazyLoadingStorageWrapper::getUser()
     */
    public function testGetUser(): void
    {
        $wrapped = $this->getWrappedStore();
        $wrapper = new LazyLoadingStorageWrapper($wrapped);
        
        $user = $wrapper->getUser("Foo");
        
        $this->assertSame($user, $wrapper->getUser("Foo"));
        
        // test shared among all instances
        
        $wrapper2 = new LazyLoadingStorageWrapper($wrapped);
        
        $this->assertSame($user, $wrapper2->getUser("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\LazyLoadingStorageWrapper::deleteUser()
     */
    public function testDeleteUser(): void
    {
        $wrapped = $this->getWrappedStore();
        $wrapper = new LazyLoadingStorageWrapper($wrapped);
        
        $this->assertNull($wrapper->deleteUser("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\LazyLoadingStorageWrapper::refreshUser()
     */
    public function testRefreshUser(): void
    {
        $refreshUser = UserMock::init("RefreshWrappedStoreUser", AuthenticatedUserInterface::class)->finalizeMock();
        $wrapped = $this->getWrappedStore($refreshUser);
        $wrapper = new LazyLoadingStorageWrapper($wrapped);
        
        $this->assertNull($wrapper->refreshUser("Foo", $refreshUser));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\LazyLoadingStorageWrapper::isStored()
     */
    public function testIsStored(): void
    {
        $wrapped = $this->getWrappedStore();
        $wrapper = new LazyLoadingStorageWrapper($wrapped);
        
        $this->assertTrue($wrapper->isStored("Foo"));
    }
    
    /**
     * Get a mocked user storage to wrap.
     * All mocked methods will be able to be called only once to assure lazy loading on get
     * 
     * @param AuthenticatedUserInterface|null $user
     *   Mocked user which will interact with the mocked user store. Set to null to generate one
     * 
     * @return UserStorageInterface
     *   Mocked user store
     */
    private function getWrappedStore(?AuthenticatedUserInterface& $user = null): UserStorageInterface
    {
        if(null === $user)
            $user = UserMock::init("UserStoredIntoWrapped", AuthenticatedUserInterface::class)->finalizeMock();
        
        return UserStorageMock::init("WrappedUserStorage")
                                    ->mockAddUser($this->once(), "Foo", $user)
                                    ->mockGetUser($this->once(), "Foo", $user)
                                    ->mockDeleteUser($this->once(), "Foo")
                                    ->mockRefreshUser($this->once(), "Foo", $user)
                                    ->mockIsStored($this->once(), "Foo", true)
                                ->finalizeMock();
    }
    
}
