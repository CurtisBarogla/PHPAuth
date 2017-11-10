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

namespace ZoeTest\Component\Security\Authentification;

use PHPUnit\Framework\TestCase;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use Zoe\Component\Internal\ReflectionTrait;
use Zoe\Component\Security\Authentification\Strategy\AuthentificationStrategyInterface;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Storage\UserStorageInteface;
use Zoe\Component\Security\User\User;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\Authentification\Authentification;
use Zoe\Component\Security\Exception\AuthentificationFailedException;
use Zoe\Component\Security\User\StorableUserInterface;
use Zoe\Component\Security\User\StorableUser;
use Zoe\Component\Security\Authentification\AuthentificationInterface;

/**
 * Authentification testcase
 * 
 * @see \Zoe\Component\Security\Authentification\Authentification
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class AuthentificationTest extends TestCase
{
    
    use ReflectionTrait;
    
    /**
     * @see \Zoe\Component\Security\Authentification\Authentification
     */
    public function testInterface(): void
    {
        $mock = $this->getMockBuilder(Authentification::class)->disableOriginalConstructor()->setMethods(["authenticate"])->getMock();
        
        $this->assertInstanceOf(AuthentificationInterface::class, $mock);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentification\Authentification::authenticate()
     */
    public function testAuthenticate(): void
    {
        $loadedUser = new User("foo", "bar");
        
        $store = $this->getMockedStorage();
        $loader = $this->getMockedLoader();
        $loader->method("loadUser")->with(new User("bar", "foo"))->will($this->returnValue($loadedUser));
        $strategy = $this->getMockedAuthentificationStrategy($loadedUser, new User("bar", "foo"), true);
        $store
            ->method("addUser")
            ->with(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUser::createFromUser($loadedUser))
            ->will($this->returnValue(null));
        
        $authentification = new Authentification($loader, $store, $strategy);
        
        $this->assertNull($authentification->authenticate(new User("bar", "foo")));
    }
    
    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentification\Authentification::authenticate()
     */
    public function testExceptionWhenLoaderDoesNotFoundAUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        
        $store = $this->getMockedStorage();
        $loader = $this->getMockedLoader();
        $loader->method("loadUser")->with(new User("bar", "foo"))->will($this->throwException(new UserNotFoundException()));
        $strategy = $this->getMockedAuthentificationStrategy(new User("foo", "bar"), new User("foo", "bar"), true);
        
        $authentification = new Authentification($loader, $store, $strategy);
        $authentification->authenticate(new User("bar", "foo"));
    }
    
    /**
     * @see \Zoe\Component\Security\Authentification\Authentification::authenticate()
     */
    public function testExceptionWhenProcessFails(): void
    {
        $this->expectException(AuthentificationFailedException::class);
        $this->expectExceptionMessage("This user 'bar' cannot be authenticated");
        
        $loadedUser = new User("foo", "bar");
        
        $store = $this->getMockedStorage();
        $loader = $this->getMockedLoader();
        $loader->method("loadUser")->with(new User("bar", "foo"))->will($this->returnValue($loadedUser));
        $strategy = $this->getMockedAuthentificationStrategy($loadedUser, new User("bar", "foo"), false);
        
        $authentification = new Authentification($loader, $store, $strategy);
        $authentification->authenticate(new User("bar", "foo"));
    }
    
    /**
     * Get a mock UserLoader
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   UserLoader mocked
     */
    private function getMockedLoader(): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(UserLoaderInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder(UserLoaderInterface::class)->disableOriginalConstructor()->setMethods($methods)->getMock();
        
        return $mock;
    }
    
    /**
     * Get a mocked authentification strategy
     * 
     * @param UserInterface $loadedUser
     *   User 1
     * @param UserInterface $user
     *   User 2
     * @param bool $strategyResult
     *   Result of the strategy implying the two given users
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked strategy
     */
    private function getMockedAuthentificationStrategy(
        UserInterface $loadedUser,
        UserInterface $user,
        bool $strategyResult): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(AuthentificationStrategyInterface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder(AuthentificationStrategyInterface::class)->setMethods($methods)->getMock();
        $mock->method("process")->with($loadedUser, $user)->will($this->returnValue($strategyResult));
        
        return $mock;
    }
    
    /**
     * Get a mocked UserStorage
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked UserStorage
     */
    private function getMockedStorage(): \PHPUnit_Framework_MockObject_MockObject
    {
        $reflection = new \ReflectionClass(UserStorageInteface::class);
        $methods = $this->reflection_extractMethods($reflection);
        
        $mock = $this->getMockBuilder(UserStorageInteface::class)->setMethods($methods)->getMock();
        
        return $mock;
    }
    
}
