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
use Zoe\Component\Security\User\Loader\LoadedUserAwareTrait;
use ZoeTest\Component\Security\MockGeneration\User\UserMock;
use Zoe\Component\Security\User\AuthenticationUserInterface;

/**
 * LoadedUserAwareTrait testcase
 * 
 * @see \Zoe\Component\Security\User\Loader\LoadedUserAwareTrait
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class LoadedUserAwareTraitTest extends TestCase
{
    
    /**
     * @see \Zoe\Component\Security\User\Loader\LoadedUserAwareTrait::getLoadedUser()
     */
    public function testGetLoadedUser(): void
    {
        $user = UserMock::init("UserSettedIntoLoadedAwareComponent", AuthenticationUserInterface::class)->finalizeMock();
        $trait = $this->getMockedTrait();
        
        $trait->setLoadedUser($user);
        
        $this->assertSame($user, $trait->getLoadedUser());
    }
    
    /**
     * @see \Zoe\Component\Security\User\Loader\LoadedUserAwareTrait::setLoadedUser()
     */
    public function testSetLoadedUser(): void
    {
        $user = UserMock::init("UserSettedIntoLoadedAwareComponent", AuthenticationUserInterface::class)->finalizeMock();
        $trait = $this->getMockedTrait();
        
        $this->assertNull($trait->setLoadedUser($user));
    }
    
    /**
     * Get a mock of the tested trait
     * 
     * @return \PHPUnit_Framework_MockObject_MockObject
     *   Mocked trait
     */
    private function getMockedTrait(): \PHPUnit_Framework_MockObject_MockObject
    {
        return $this->getMockForTrait(LoadedUserAwareTrait::class);
    }
    
}
