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

use Zoe\Component\Security\Exception\User\UserNotFoundException;
use Zoe\Component\Security\User\AuthenticationUserInterface;

/**
 * Responsible to load users from various sources
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface UserLoaderInterface
{
    
    /**
     * Load user from external source - NEVER responsible to validate user (password checking...).
     * Returned user MUST always be initialized from external sources informations and never from the given one
     * 
     * @param AuthenticationUserInterface $user
     *   User to load
     *   
     * @return AuthenticationUserInterface
     *   User found
     *   
     * @throws UserNotFoundException
     *   If no user can be loaded
     */
    public function loadUser(AuthenticationUserInterface $user): AuthenticationUserInterface;
    
}
