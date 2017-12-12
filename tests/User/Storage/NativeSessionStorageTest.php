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

// mock native session functions
namespace Zoe\Component\Security\User\Storage {
    
global $error;

/**
 * Mock session status to simulate session not started if needed
 * 
 * @return int
 */
function session_status(): int 
{
    global $error;
    
    if($error)
        return PHP_SESSION_NONE;
    else
        return PHP_SESSION_ACTIVE;
}

/**
 * Mock session regeneration call
 * 
 * @param mixed $delete_old_session
 * @return bool
 */
function session_regenerate_id($delete_old_session = null): bool
{
    return true;
}
    
};

namespace ZoeTest\Component\Security\User\Storage {
    
use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\User\Storage\NativeSessionStorage;
use Zoe\Component\Internal\ReflectionTrait;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\User\AuthenticatedUserInterface;
use Zoe\Component\Security\Exception\User\UserNotFoundException;
use Zoe\Component\Security\User\AuthenticatedUser;
                                
class NativeSessionStorageTest extends TestCase
{
    
    use ReflectionTrait;
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::addUser()
     */
    public function testAddUser(): void
    {
        $user = UserMock::init("UserStored", AuthenticatedUserInterface::class)->finalizeMock();
        // no refresh
        $store = $this->getStore();
        $this->assertNull($store->addUser("Foo", $user));
        
        /// with refresh
        $store = $this->getStore(false, true, false);
        $this->assertNull($store->addUser("Foo", $user));
        
        // json
        $store = $this->getStore(false, false, true);
        // need concrete object here, cannot mock json call...
        $this->assertNull($store->addUser("Foo", new AuthenticatedUser("Foo", new \DateTime())));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::getUser()
     */
    public function testGetUser(): void
    {
        $user = UserMock::init("UserStoredAndGetted", AuthenticatedUserInterface::class)->finalizeMock();
        $store = $this->getStore(false, false, false, $user);
        
        $this->assertSame($user, $store->getUser("Foo"));
        
        // json
        $time = \DateTime::createFromFormat("U", (string)\time());
        $user = new AuthenticatedUser("Foo", $time);
        $json = \json_encode($user);
        $store = new NativeSessionStorage(false, true);
        $reflection = new \ReflectionClass($store);
        $this->reflection_injectNewValueIntoProperty($store, $reflection, "session", ["Foo" => $json]);
        $this->assertEquals($user, $store->getUser("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::deleteUser()
     */
    public function testDeleteUser(): void
    {
        $user = UserMock::init("UserStoredAndDeleted", AuthenticatedUserInterface::class)->finalizeMock();
        $store = $this->getStore(false, false, false, $user);
        
        $this->assertNull($store->deleteUser("Foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::refreshUser()
     */
    public function testRefreshUser(): void
    {
        $user = UserMock::init("UserStoredAndRefreshed", AuthenticatedUserInterface::class)->finalizeMock();
        $freshUser = UserMock::init("FreshUser", AuthenticatedUserInterface::class)->mockGetName($this->exactly(2), "Bar")->finalizeMock();
        // no refresh
        $store = $this->getStore(false, false, false, $user);
        
        $this->assertNull($store->refreshUser("Foo", $freshUser));
        $this->assertSame("Bar", $store->getUser("Foo")->getName());
        
        // with refresh
        $store = $this->getStore(false, true, false, $user);
        
        $this->assertNull($store->refreshUser("Foo", $freshUser));
        $this->assertSame("Bar", $store->getUser("Foo")->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::isStored()
     */
    public function testIsStored(): void
    {
        $user = UserMock::init("StoredUser", AuthenticatedUserInterface::class)->finalizeMock();
        $store = $this->getStore();
        
        $this->assertFalse($store->isStored("Foo"));
        
        $store->addUser("Foo", $user);
        
        $this->assertTrue($store->isStored("Foo"));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::__construct()
     */
    public function testExceptionWhenSessionNoEnabled(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("Session MUST be active before able to store user into NativeSessionStorage");
        
        $store = $this->getStore(true);
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::getUser()
     */
    public function testExceptionGetUserWhenNoUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("No user has been found into the store");
        
        $store = $this->getStore();
        $store->getUser("Foo");
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::getUser()
     */
    public function testExceptionGetUserWhenUserErrorDuringGettingProcess(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("User not found");
        
        $store = $this->getStore();
        $reflection = new \ReflectionClass($store);
        $this->reflection_injectNewValueIntoProperty($store, $reflection, "session", ["Foo" => "{}"]);
        $store->getUser("Foo");
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::deleteUser()
     */
    public function testExceptionDeleteUserWhenNoUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("No user has been found into the store");
        
        $store = $this->getStore();
        $store->deleteUser("Foo");
    }
    
    /**
     * @see \Zoe\Component\Security\User\Storage\NativeSessionStorage::refreshUser()
     */
    public function testExceptionRefreshUserWhenNoUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'Foo' has been not found into the store");
        
        $user = UserMock::init("Exception", AuthenticatedUserInterface::class)->mockGetName($this->once(), "Foo")->finalizeMock();
        $store = $this->getStore();
        $store->refreshUser("Foo", $user);
    }
    
    /**
     * Inject basic array instead of the default setted $_SESSION
     * 
     * @param bool $sessionError
     *   Set to true to set session to error
     * @param bool $refresh
     *   If store session id is refresh
     * @param bool $json
     *   If json is used to store users
     * @param AuthenticatedUserInterface|null $user
     *   User setted into the store by default with 'Foo' identifier. Set to null to skip
     * 
     * @return NativeSessionStorage
     *   Store setted
     */
    private function getStore(
        bool $sessionError = false,
        bool $refresh = false,
        bool $json = false,
        ?AuthenticatedUserInterface $user = null): NativeSessionStorage
    {
        // disable error from session
        global $error;
        $error = $sessionError;
        
        $session = [];
        $store = new NativeSessionStorage($refresh, $json);
        $reflection = new \ReflectionClass($store);
        $this->reflection_injectNewValueIntoProperty($store, $reflection, "session", $session);
        if(null !== $user)
            $store->addUser("Foo", $user);
        
        return $store;
    }
    
}

};
