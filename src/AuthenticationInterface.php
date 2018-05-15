<?php
//StrictType
declare(strict_types = 1);

/*
 * Ness
 * Authentication component
 *
 * Author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
 
namespace Ness\Component\Authentication;

use Ness\Component\Authentication\User\AuthenticatedUserInterface;
use Ness\Component\Authentication\Exception\AuthenticationFailedException;
use Ness\Component\User\UserInterface;

/**
 * Provide a solution to convert an "anonymous" user into an authenticated one.
 * If sensitives informations are passed into the authentication process, they MUST be purged
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationInterface
{
    
    /**
     * Authenticate an user and convert it
     * 
     * @param UserInterface $user
     *   User to authenticate
     * 
     * @return AuthenticatedUserInterface
     *   Authenticated user
     *   
     * @throws AuthenticationFailedException
     *   When the user cannot be authenticated
     */
    public function authenticate(UserInterface $user): AuthenticatedUserInterface;
    
    /**
     * Check if a given user is considered authenticated
     * 
     * @param UserInterface $user
     *   User to check
     * 
     * @return bool
     *   True if the user is considered authenticated. False otherwise
     */
    public function isAuthenticated(UserInterface $user): bool;
    
}
