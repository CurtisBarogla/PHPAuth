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

namespace Ness\Component\Authentication\User;

use Ness\Component\User\UserInterface;
use Ness\Component\Authentication\Exception\UserCredentialNotFoundException;

/**
 * User given to the authentication process.
 * User into this state MUST be immutable
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface AuthenticationUserInterface extends UserInterface
{
    
    /**
     * Get password
     * 
     * @return string
     *   User password
     *   
     * @throws UserCredentialNotFoundException
     *   When no password has been defined for this user
     */
    public function getPassword(): string;
    
    /**
     * Get a specific credential for the user
     * 
     * @param string $credential
     *   Credential name
     * 
     * @return mixed
     *   Credential content
     *   
     * @throws UserCredentialNotFoundException
     *   When credential is not setted
     */
    public function getCredential(string $credential);
    
}
