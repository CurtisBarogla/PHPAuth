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

namespace Zoe\Component\Security\Storage {
    function session_regenerate_id(bool $delete_old_session = null): bool
    {
        return true;
    }
}

namespace ZoeTest\Component\Security\Storage {

    use PHPUnit\Framework\TestCase;
    use Zoe\Component\Internal\ReflectionTrait;
    use Zoe\Component\Security\Storage\NativeSessionStorage;
    use Zoe\Component\Security\Storage\UserStorageInteface;
    use Zoe\Component\Security\User\StorableUser;
    use Zoe\Component\Security\Exception\UserNotFoundException;
    use Zoe\Component\Security\User\StorableUserInterface;
    use Zoe\Component\Security\Exception\LogicException;
    
    /**
     * NativeSessionStorage testcase
     * 
     * @see \Zoe\Component\Security\Storage\NativeSessionStorage
     * 
     * @author CurtisBarogla <curtis_barogla@outlook.fr>
     *
     */
    class NativeSessionStorageTest extends TestCase
    {
        
        use ReflectionTrait;
        
        /**
         * @see \Zoe\Component\Security\Storage\NativeSessionStorage
         */
        public function testInterface(): void
        {
            $store = $this->getReflectiveStore();
            
            $this->assertInstanceOf(UserStorageInteface::class, $store);
        }
        
        /**
         * @see \Zoe\Component\Security\Storage\NativeSessionStorage::addUser()
         */
        public function testAddUser(): void
        {
            $reflection = null;
            $store = null;
            $store = $this->getReflectiveStore($reflection, $store, true);
            
            $this->assertNull($store->addUser("foo", new StorableUser("foo", [], [])));
            $this->assertCount(1, $this->reflection_getPropertyValue($store, $reflection, "session"));
        }
        
        /**
         * @see \Zoe\Component\Security\Storage\NativeSessionStorage::getUser()
         */
        public function testGetUser(): void
        {
            $store = $this->getReflectiveStore();
            
            $store->addUser("foo", new StorableUser("foo", [], []));
            $expected = new StorableUser("foo", [], []);
            
            $this->assertEquals($expected, $store->getUser("foo"));
        }
        
        /**
         * @see \Zoe\Component\Security\Storage\NativeSessionStorage::deleteUser()
         */
        public function testDeleteUser(): void
        {
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage("Cannot delete user from this storage as php is responsible to expire invalids sessions");
            
            $store = new NativeSessionStorage();
            $store->deleteUser("foo");
        }
        
        /**
         * @see \Zoe\Component\Security\Storage\NativeSessionStorage::refreshUser()
         */
        public function testRefreshUser(): void
        {
            $store = $this->getReflectiveStore();
            
            $expected = new StorableUser("foo", [], []);
            $store->addUser("foo", new StorableUser("foo", [], []));
            $this->assertEquals($expected, $store->getUser("foo"));
            $expected = new StorableUser("bar", [], []);
            $this->assertNull($store->refreshUser("foo", new StorableUser("bar", [], [])));
            $this->assertEquals($expected, $store->getUser("foo"));
        }
        
                        /**_____EXCEPTION_____**/
        
        /**
         * @see \Zoe\Component\Security\Storage\NativeSessionStorage::getUser()
         */
        public function testExceptionGetUserWhenInvalid(): void
        {
            $this->doTestException("getUser", "foo");
        }
        
        /**
         * @see \Zoe\Component\Security\Storage\NativeSessionStorage::refreshUser()
         */
        public function testExceptionRefreshUserWhenInvalid(): void
        {
            $this->doTestException("refreshUser", "foo", new StorableUser("foo", [], []));
        }
        
        /**
         * Test exception thrown
         * 
         * @param string $method
         *   Method throwing the exception
         * @param StorableUserInterface|string ...$args
         *   Args passed to the method call (arg0 MUST be the userIdentifier)
         */
        private function doTestException(string $method, ...$args): void
        {
            $this->expectException(UserNotFoundException::class);
            $this->expectExceptionMessage("No user found into the store for this identifier '{$args[0]}'");
            
            $store = $this->getReflectiveStore();
            $store->{$method}(...$args);
        }
            
        /**
         * Get a NativeSessionStorage with "mocked" session setted via Reflection
         * 
         * @param \ReflectionClass|null $reflection
         *   Reflection that can be getted/setted by reference after initialization
         * @param NativeSessionStorage|null $store
         *   Store that can be getted/setted by reference after initialization
         * 
         * @return NativeSessionStorage
         *   Storage with array session setted
         */
        private function getReflectiveStore(
            ?\ReflectionClass& $reflection = null, 
            ?NativeSessionStorage& $store = null,
            bool $refresh = false): NativeSessionStorage
        {
            $store = new NativeSessionStorage($refresh);
            $reflection = new \ReflectionClass($store);
            $this->reflection_injectNewValueIntoProperty($store, $reflection, "session", []);
            
            return $store;
        }
        
    }
}
