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

use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\Exception\AuthenticationFailedException;

/**
 * Responsible to authenticate user and store it
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationInterface
{
    
    /**
     * Authenticate a user and store it
     * 
     * @param UserInterface $user
     *   User to authenticate
     *   
     * @throws AuthenticationFailedException
     *   When the given has been found, but is invalid
     * @throws UserNotFoundException
     *   When the given user is not setted
     */
    public function authenticate(UserInterface $user): void;
    
}
