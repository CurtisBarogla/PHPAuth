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

namespace ZoeTest\Component\Security\MockGeneration\Tests\User;

use PHPUnit\Framework\TestCase;
use ZoeTest\Component\Security\MockGeneration\User\UserLoaderMock;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\Exception\User\UserNotFoundException;

/**
 * UserLoadMock testcase
 * 
 * @see \ZoeTest\Component\Security\MockGeneration\User\UserLoaderMock
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderMockTest extends TestCase
{
    
    /**
     * @see \ZoeTest\Component\Security\MockGeneration\User\UserLoaderMock::mockLoadUser()
     */
    public function testMockLoadUser(): void
    {
        $userGiven = UserMock::init("UserGivenToLoader", AuthenticationUserInterface::class)->finalizeMock();
        $userReturned = UserMock::init("UserReturnedByLoader", AuthenticationUserInterface::class)->mockGetName($this->once(), "Foo")->finalizeMock();
        $loader = UserLoaderMock::init("Foo")->mockLoadUser($this->once(), $userGiven, $userReturned)->finalizeMock();
        
        $this->assertSame("Foo", $loader->loadUser($userGiven)->getName());
        
        $this->expectException(UserNotFoundException::class);
        $userGiven = UserMock::init("UserGivenToLoader", AuthenticationUserInterface::class)->finalizeMock();
        $userReturned = null;
        $loader = UserLoaderMock::init("Foo")->mockLoadUser($this->once(), $userGiven, $userReturned)->finalizeMock();
        
        $loader->loadUser($userGiven);
    }
    
}
