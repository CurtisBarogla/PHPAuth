<?php
//StrictType
declare(strict_types = 1);

/*
 * Zoe
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */

namespace Zoe\Component\Authentication;

use Zoe\Component\User\AuthenticationUserInterface;
use Zoe\Component\User\AuthenticatedUserInterface;
use Zoe\Component\Authentication\Exception\AuthenticationFailedException;
use Zoe\Component\User\UserInterface;

/**
 * Responsible to authenticate user
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationInterface
{
    
    /**
     * Authenticate a user.
     * All informations setted into the authenticated user MUST be setted from an extra source and never from the given user
     * 
     * @param AuthenticationUserInterface $user
     *   User to authenticate
     *   
     * @return AuthenticatedUserInterface
     *   Authenticated user
     *   
     * @throws AuthenticationFailedException
     *   When user cannot be authenticated
     */
    public function authenticate(AuthenticationUserInterface $user): AuthenticatedUserInterface;
    
    /**
     * Check if a user is already considered authenticated by the authentication process
     * 
     * @param UserInterface $user
     *   User to check if authenticated
     * 
     * @return bool
     *   True if the user is considered authenticated. False otherwise
     */
    public function isAuthenticated(UserInterface $user): bool;
    
}
