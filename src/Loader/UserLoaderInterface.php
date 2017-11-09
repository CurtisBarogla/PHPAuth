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

namespace Zoe\Component\Security\Loader;

use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\UserInterface;

/**
 * Responsible to load user from a users store
 * A loader is not responsible to authenticate a user. Just initialize it if possible
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface UserLoaderInterface
{
        
    /**
     * Load a user and return it setted
     * 
     * @param UserInterface $user
     *   User instance to load
     * 
     * @return UserInterface
     *   User with values setted
     *   
     * @throws UserNotFoundException
     *   When no user has been found for the given user
     */
    public function loadUser(UserInterface $user): UserInterface;
    
}
