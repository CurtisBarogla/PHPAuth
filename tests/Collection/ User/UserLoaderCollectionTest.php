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

namespace ZoeTest\Component\Security\Collection\User;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Collection\User\UserLoaderCollection;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\UserInterface;

/**
 * UserLoaderCollection testcase
 * 
 * @see \Zoe\Component\Security\Collection\User\UserLoaderCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderCollectionTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Collection\User\UserLoaderCollection::add()
     */
    public function testAdd(): void
    {
        $loader = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"));
        
        $collection = new UserLoaderCollection();
        $this->assertNull($collection->add($loader));
    }
    
    /**
     * @see \Zoe\Component\Security\Collection\User\UserLoaderCollection::getUser()
     */
    public function testGetUser(): void
    {
        $loaderException = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"), true);
        $loaderFound = $this->getMockedUserLoader("bar", $this->getMockedUser("foo", "bar"), false);
        
        $collection = new UserLoaderCollection();
        $collection->add($loaderException);
        $collection->add($loaderFound);
        
        $this->assertInstanceOf(UserInterface::class, $collection->getUser($this->getMockedUser("foo", "bar")));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Collection\User\UserLoaderCollection::getUser()
     */
    public function testExceptionGetUserNoLoaderFoundOne(): void
    {
        $loader1 = $this->getMockedUserLoader("foo", $this->getMockedUser("foo", "bar"), true);
        $loader2 = $this->getMockedUserLoader("bar", $this->getMockedUser("foo", "bar"), true);
        
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'foo' has been not found into the setted loaders 'foo, bar'");
        
        $collection = new UserLoaderCollection();
        $collection->add($loader1);
        $collection->add($loader2);
        
        $collection->getUser($this->getMockedUser("foo", "bar"));
    }
    
}
