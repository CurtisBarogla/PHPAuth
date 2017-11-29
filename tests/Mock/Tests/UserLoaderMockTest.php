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

namespace ZoeTest\Component\Security\Mock\Tests;

use ZoeTest\Component\Security\SecurityTestCase;
use ZoeTest\Component\Security\Mock\UserLoaderMock;
use ZoeTest\Component\Security\Mock\UserMock;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;

/**
 * UserLoaderMock testcase
 * 
 * @see \ZoeTest\Component\Security\Mock\UserLoaderMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderMockTest extends SecurityTestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserLoaderMock::mockLoadUser()
     */
    public function testMockLoadUser(): void
    {
        $userGiven = UserMock::initMock(UserInterface::class, "Foo")->finalizeMock();
        $userReturn = UserMock::initMock(MutableUserInterface::class, "Foo")->finalizeMock();
        
        $loader = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $userGiven, $userReturn)->finalizeMock();
        
        $this->assertEquals($userReturn, $loader->loadUser($userGiven));
        
        $this->expectException(UserNotFoundException::class);
        
        $loader = UserLoaderMock::initMock("Foo")->mockLoadUser($this->once(), $userGiven, null)->finalizeMock();
        $loader->loadUser($userGiven);
    }
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserLoaderMock::mockIdentify()
     */
    public function testMockIdentify(): void
    {
        $loader = UserLoaderMock::initMock("Foo")->mockIdentify($this->any())->finalizeMock();
        
        $this->assertSame("Foo", $loader->identify());
    }
    
                    /**_____EXCEPTIONS_____**/
    
    /**
     * @see \ZoeTest\Component\Security\Mock\UserLoaderMock()
     */
    public function testExceptionWhenMethodAlreadyMocked(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage("This method 'identify' for UserLoader 'Foo' has been alerady mocked");
        
        $loader = UserLoaderMock::initMock("Foo")->mockIdentify($this->any())->mockIdentify($this->any())->finalizeMock();
    }
    
}
