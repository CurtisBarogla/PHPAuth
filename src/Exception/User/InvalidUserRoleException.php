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

namespace Zoe\Component\Security\Exception\User;

use Zoe\Component\Security\User\UserInterface;

class InvalidUserRoleException extends UserException
{
    
    /**
     * Invalid role
     * 
     * @var string
     */
    private $roles;
    
    /**
     * Initialize exception
     * 
     * @param UserInterface $user
     *   User which error happen
     * @param string $role
     *   Invalid role
     */
    public function __construct(UserInterface $user, string $role)
    {
        $this->role = $role;
        parent::__construct($user);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Exception\User\UserException::throwMessage()
     */    
    protected function throwMessage(): string
    {
        return \sprintf("This role '%s' for user '%s' is not setted",
            $this->role,
            $this->user->getName());
    }

}
