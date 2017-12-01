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

namespace ZoeTest\Component\Security\User\Loader;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Loader\UserLoaderCollection;
use ZoeTest\Component\Security\Mock\UserLoaderMock;
use ZoeTest\Component\Security\Mock\UserMock;

/**
 * UserLoaderCollection testcase
 * 
 * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderCollectionTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::addLoader()
     */
    public function testAddLoader(): void
    {
        $loader = UserLoaderMock::initMock("Foo")->finalizeMock();
        
        $collection = new UserLoaderCollection("foo", $loader);
        $this->assertNull($collection->add($loader));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testLoadUser(): void
    {
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockGetName($this->once())->finalizeMock();
        $userReturned = UserMock::initMock(MutableUserInterface::class, "Foo")->mockGetName($this->once())->finalizeMock();
        $loader1 = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $user, null)->finalizeMock();
        $loader2 = UserLoaderMock::initMock("Bar")->mockLoadUser($this->once(), $user, $userReturned)->finalizeMock();

        $collection = new UserLoaderCollection("foo", $loader1);
        $collection->add($loader2);
            
        $userLoaded = $collection->loadUser($user);
        
        $this->assertInstanceOf(MutableUserInterface::class, $userLoaded);
        $this->assertSame("Foo", $userLoaded->getName());
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::identify()
     */
    public function testIdentify(): void
    {
        $loader = UserLoaderMock::initMock("Foo")->finalizeMock();
        
        $collection = new UserLoaderCollection("Foo", $loader);
        
        $this->assertSame("Foo", $collection->identify());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testExceptionWhenNoLoaderHasBeenAbleToLoadAUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'Foo' does not exist for the given loaders 'Foo, Bar'");
        
        $user = UserMock::initMock(UserInterface::class, "Foo")->mockGetName($this->any())->finalizeMock();
        $loader1 = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $user, null)->mockIdentify($this->once())->finalizeMock();
        $loader2 = UserLoaderMock::initMock("Bar")->mockLoadUser($this->once(), $user, null)->mockIdentify($this->once())->finalizeMock();
        
        $collection = new UserLoaderCollection("Foo", $loader1);
        $collection->add($loader2);
        
        $collection->loadUser($user);
    }
    
}
