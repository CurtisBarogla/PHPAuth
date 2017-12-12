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

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\User\UserLoaderMock;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\User\Loader\UserLoaderCollection;
use Zoe\Component\Security\Exception\User\UserNotFoundException;

/**
 * UserLoaderCollection testcase
 * 
 * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderCollectionTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::addLoader()
     */
    public function testAddLoader(): void
    {
        $loaderDefault = UserLoaderMock::init("DefaultLoader")->finalizeMock();
        $loaderAdded = UserLoaderMock::init("LoaderAdded")->finalizeMock();
        
        $loader = new UserLoaderCollection($loaderDefault);
        $this->assertNull($loader->addLoader($loaderAdded));
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testLoadUser(): void
    {
        $userGiven = UserMock::init("UserGiveToLoader", AuthenticationUserInterface::class)
                                ->mockGetName($this->exactly(3), "Foo")
                            ->finalizeMock();
        $userReturned = UserMock::init("UserReturnedByLoader", AuthenticationUserInterface::class)
                                ->mockGetName($this->once(), "Foo")
                            ->finalizeMock();
        $loaderException = UserLoaderMock::init("UserLoaderException")->mockLoadUser($this->once(), $userGiven, null)->finalizeMock();
        $loaderFound = UserLoaderMock::init("UserLoaderFound")->mockLoadUser($this->once(), $userGiven, $userReturned)->finalizeMock();
        $loaderPassed = UserLoaderMock::init("UserLoaderPassed")->mockLoadUser($this->never(), $userGiven, $userReturned)->finalizeMock();
        
        $loader = new UserLoaderCollection($loaderException);
        $loader->addLoader($loaderFound);
        $loader->addLoader($loaderPassed);
        
        $user = $loader->loadUser($userGiven);
        $this->assertInstanceOf(AuthenticationUserInterface::class, $user);
        $this->assertSame("Foo", $user->getName());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \Zoe\Component\Security\User\Loader\UserLoaderCollection::loadUser()
     */
    public function testExceptionLoadUserWhenNoLoaderCanLoadAUser(): void
    {
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("This user 'Foo' cannot be loaded");
        
        $userGiven = UserMock::init("UserGivenToLoader", AuthenticationUserInterface::class)->mockGetName($this->exactly(4), "Foo")->finalizeMock();
        
        $loaderFail1 = UserLoaderMock::init("LoaderFail1")->mockLoadUser($this->once(), $userGiven, null)->finalizeMock();
        $loaderFail2 = UserLoaderMock::init("LoaderFail2")->mockLoadUser($this->once(), $userGiven, null)->finalizeMock();
        $loaderFail3 = UserLoaderMock::init("LoaderFail3")->mockLoadUser($this->once(), $userGiven, null)->finalizeMock();
        
        $loader = new UserLoaderCollection($loaderFail1);
        $loader->addLoader($loaderFail2);
        $loader->addLoader($loaderFail3);
        
        $loader->loadUser($userGiven);
    }
    
}