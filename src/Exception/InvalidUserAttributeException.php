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
 * InvalidUserAttributeException Security component
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidUserAttributeException extends \Exception
{
    
    /**
     * Initialize exception
     * 
     * @param UserInterface $user
     *   User which the attribute is invalid
     * @param string $invalidAttribute
     *   Invalid attribute name
     */
    public function __construct(UserInterface $user, string $invalidAttribute)
    {
        parent::__construct(\sprintf("This attribute '%s' for the user '%s' is not setted",
            $invalidAttribute,
            $user->getName()));
    }
    
}
