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
use Zoe\Component\Security\User\Loader\UserLoaderCollection;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;

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
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testLoadUser(): void
    {
        $collection = $this->getSettedUserLoaderCollection(5, [true, true, true, true, false]);
        
        $this->assertInstanceOf(UserInterface::class, $collection->loadUser($this->getMockedUser("foo", "bar")));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testLoadUserFails(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'foo' does not exist for the given loaders '0, 1, 2, 3'");
        
        $collection = $this->getSettedUserLoaderCollection(4, [true, true, true, true]);
        
        $collection->loadUser($this->getMockedUser("foo", "bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testLoadUserWithNoLoader(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("No loader has been registered into the collection 'foo'");
        
        $collection = new UserLoaderCollection("foo");
        $collection->loadUser($this->getMockedUser("foo", "bar"));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::identify()
     */
    public function testIdentify(): void
    {
        $collection = new UserLoaderCollection("foo");
        
        $this->assertSame("foo", $collection->identify());
    }
    
    /**
     * Get a UserLoaderCollection with a number of UserLoader mocked setted into it
     * 
     * @param int $countMockedLoaders
     *   Number of loaders to mock
     * @param bool[] $exceptionThrownByLoaders
     *   If the loader must throw an exception
     * 
     * @return UserLoaderCollection
     *   Collection with mocked UserLoader setted
     */
    private function getSettedUserLoaderCollection(int $countMockedLoaders, array $exceptionThrownByLoaders): UserLoaderCollection
    {
        $user = $this->getMockedUser("foo", "bar");
        $collection = new UserLoaderCollection("foo");
        for ($i = 0; $i < $countMockedLoaders; $i++) {
            $loader = $this->getMockedUserLoader((string) $i, $user, $exceptionThrownByLoaders[$i]);
            $collection->add($loader);
        }
        
        return $collection;
    }
    
}
