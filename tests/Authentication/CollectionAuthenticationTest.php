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

namespace ZoeTest\Component\Security\Authentication;

use ZoeTest\Component\Security\SecurityTestCase;
use Zoe\Component\Security\Collection\User\UserLoaderCollection;
use Zoe\Component\Security\Storage\UserStorageInteface;
use Zoe\Component\Security\User\StorableUserInterface;
use Zoe\Component\Security\User\StorableUser;
use Zoe\Component\Security\Collection\Strategy\AuthenticationStrategyCollection;
use Zoe\Component\Security\Authentication\CollectionAuthentication;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\Exception\AuthenticationFailedException;

/**
 * CollectionAuthentication testcase
 * 
 * @see \Zoe\Component\Security\Authentication\CollectionAuthentication
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class CollectionAuthenticationTest extends SecurityTestCase
{
    
    /**
     * @see \Zoe\Component\Security\Authentication\CollectionAuthentication::authenticate()
     */
    public function testAuthenticate(): void
    {
        $user = $this->getMockedUser("foo", "bar");
        
        $loaders = $this->getMockBuilder(UserLoaderCollection::class)->setMethods(["getUser"])->getMock();
        $loaders->method("getUser")->with($user)->will($this->returnValue($user));
        $strategies = $this->getMockBuilder(AuthenticationStrategyCollection::class)->setMethods(["process"])->getMock();
        $strategies->method("process")->with($user, $user)->will($this->returnValue(true));
        $store = $this
            ->getMockBuilder(UserStorageInteface::class)
            ->setMethods($this->reflection_extractMethods(new \ReflectionClass(UserStorageInteface::class)))
            ->getMock();
        $store
            ->expects($this->once())
            ->method("refreshUser")
            ->with(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUser::createFromUser($user))
            ->will($this->throwException(new UserNotFoundException()));
        $store
            ->expects($this->once())
            ->method("addUser")
            ->with(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUser::createFromUser($user))
            ->will($this->returnValue(null));
        
        $collectionAuthentication = new CollectionAuthentication($loaders, $strategies);
        $collectionAuthentication->setStorage($store);
        
        $this->assertNull($collectionAuthentication->authenticate($user));
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\Authentication\CollectionAuthentication::authenticate()
     */
    public function testExceptionAuthenticationWhenLoadersFailToReturnAUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        
        $user = $this->getMockedUser("foo", "bar");
        $collection = $this->getMockBuilder(UserLoaderCollection::class)->setMethods(["getUser", "add"])->getMock();
        $collection->expects($this->once())->method("getUser")->will($this->throwException(new UserNotFoundException()));
        $strategies = $this->getMockBuilder(AuthenticationStrategyCollection::class)->getMock();
        
        $collectionAuthentication = new CollectionAuthentication($collection, $strategies);
        $collectionAuthentication->authenticate($user);
    }
    
    /**
     * @see \Zoe\Component\Security\Authentication\CollectionAuthentication::authenticate()
     */
    public function testExceptionAuthenticateWhenStrategiesFail(): void
    {
        $user = $this->getMockedUser("foo", "foo");
        
        $this->expectException(AuthenticationFailedException::class);
        $this->expectExceptionMessage("This user 'foo' cannot be authenticated");
        $strategies = $this->getMockBuilder(AuthenticationStrategyCollection::class)->setMethods(["process"])->getMock();
        $strategies->expects($this->once())->method("process")->with($user, $user)->will($this->returnValue(false));
        $loaders = $this->getMockBuilder(UserLoaderCollection::class)->setMethods(["getUser"])->getMock();
        $loaders->method("getUser")->with($user)->will($this->returnValue($user));
        
        $collectionAuthentication = new CollectionAuthentication($loaders, $strategies);
        $collectionAuthentication->authenticate($user);
    }
    
}
    