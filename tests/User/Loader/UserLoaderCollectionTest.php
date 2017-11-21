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
        $loader = $this->getMockedUserLoader("foo");
        
        $collection = new UserLoaderCollection("foo", $loader);
        $this->assertNull($collection->add($loader));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testLoadUser(): void
    {
        $user = $this->getMockedUser(UserInterface::class, "foo");
        $loader1 = $this->getMockedUserLoader("loader1");
        $loader2 = $this->getMockedUserLoader("loader2");
        $loader1->expects($this->once())->method("loadUser")->with($user)->will($this->throwException(new UserNotFoundException()));
        $loader2
            ->expects($this->once())
            ->method("loadUser")
            ->with($user)
            ->will($this->returnValue($this->getMockedUser(MutableUserInterface::class, "foo", true, 1, 1)));
        
        $collection = new UserLoaderCollection("foo", $loader1);
        $collection->add($loader2);
            
        $userLoaded = $collection->loadUser($user);
        
        $this->assertInstanceOf(MutableUserInterface::class, $userLoaded);
        $this->assertSame("foo", $userLoaded->getName());
        $this->assertSame(["foo" => "foo"], $userLoaded->getRoles());
        $this->assertTrue($userLoaded->isRoot());
        $this->assertSame(["foo" => "bar"], $userLoaded->getAttributes());
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::identify()
     */
    public function testIdentify(): void
    {
        $collection = new UserLoaderCollection("foo", $this->getMockedUserLoader("foo"));
        
        $this->assertSame("foo", $collection->identify());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testExceptionWhenNoLoaderHasBeenAbleToLoadAUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'foo' does not exist for the given loaders 'loader1, loader2'");
        
        $user = $this->getMockedUser(UserInterface::class, "foo");

        $loader1 = $this->getMockedUserLoader("loader1");
        $loader1->expects($this->once())->method("loadUser")->with($user)->will($this->throwException(new UserNotFoundException()));
        $loader1->expects($this->once())->method("identify")->will($this->returnValue("loader1"));
        $loader2 = $this->getMockedUserLoader("loader2");
        $loader2->expects($this->once())->method("loadUser")->with($user)->will($this->throwException(new UserNotFoundException()));
        $loader2->expects($this->once())->method("identify")->will($this->returnValue("loader2"));
        
        $collection = new UserLoaderCollection("foo", $loader1);
        $collection->add($loader2);
        
        $collection->loadUser($this->getMockedUser(UserInterface::class, "foo"));
    }
    
}
