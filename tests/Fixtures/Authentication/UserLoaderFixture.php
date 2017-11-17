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

use Zoe\Component\Security\User\MutableUser;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;

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
    public function loadUser(UserInterface $user): MutableUserInterface
    {
        return new MutableUser("foo");
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
