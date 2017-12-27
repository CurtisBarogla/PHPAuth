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

namespace Zoe\Component\Security\Authentication;

use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\User\AuthenticatedUserInterface;
use Zoe\Component\Security\Exception\Authentication\AuthenticationFailedException;

/**
 * Responsible to authenticate a user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationInterface
{
    
    /**
     * Authenticate a user and return it as a authenticated state.
     * All informations setted into the authenticated user must be loaded from external source and never from the given one
     * 
     * @param AuthenticationUserInterface $user
     *   User to authenticate
     * 
     * @return AuthenticatedUserInterface
     *   User authenticated with all informations about it setted into it
     *   
     * @throws AuthenticationFailedException
     *   When the user cannot be authenticated
     */
    public function authenticate(AuthenticationUserInterface $user): AuthenticatedUserInterface;
    
}
