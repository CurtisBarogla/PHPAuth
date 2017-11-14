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

namespace ZoeTest\Component\Security\Fixtures\Authentication;

use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\User\User;

/**
 * For testing purpose only
 * 
 * @see \ZoeTest\Component\Security\Authentication\AuthenticationTest
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderFixture implements UserLoaderInterface
{
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Loader\UserLoaderInterface::loadUser()
     */
    public function loadUser(UserInterface $user): UserInterface
    {
        return new User("foo", "bar");  
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Loader\UserLoaderInterface::identify()
     */
    public function identify(): string
    {
        return "UserLoaderFixture";
    }

}
