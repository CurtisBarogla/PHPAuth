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

namespace Zoe\Component\Security\Exception;

use Zoe\Component\Security\User\Contracts\UserInterface;

/**
 * InvalidUserCredentialException Security component
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidUserCredentialException extends InvalidArgumentException
{
    
    /**
     * Initialize exception
     *
     * @param UserInterface $user
     *   User which the credential is invalid
     * @param string $invalidCredential
     *   Invalid credential name
     */
    public function __construct(UserInterface $user, string $invalidCredential)
    {
        parent::__construct(\sprintf("This credential '%s' for the user '%s' is not setted",
            $invalidCredential,
            $user->getName()));
    }
}
