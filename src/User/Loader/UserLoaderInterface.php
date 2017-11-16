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

namespace Zoe\Component\Security\User\Loader;

use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;

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
     * Load a user and return it setted.
     * MUST always load informations about a user from an external source and never from the given user
     * 
     * @param UserInterface $user
     *   User instance to load
     * 
     * @return MutableUserInterface
     *   User with values setted
     *   
     * @throws UserNotFoundException
     *   When no user has been found for the given user
     */
    public function loadUser(UserInterface $user): MutableUserInterface;
    
    /**
     * Set an identifier for the loader
     * 
     * @return string
     *   Identifier for the loader
     */
    public function identify(): string;
    
}
