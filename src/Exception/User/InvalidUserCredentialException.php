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

/**
 * User credential invalid or non-setted
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class InvalidUserCredentialException extends UserException
{
    
    /**
     * Invalid credential
     * 
     * @var string
     */
    private $credential;
    
    /**
     * Initialize exception
     * 
     * @param UserInterface|null $user
     *   User which error happen
     * @param string $credential
     *   Invalid credential name
     */
    public function __construct(?UserInterface $user, string $credential)
    {
        $this->credential = $credential;
        parent::__construct($user);
    }
    
    /** 
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Exception\User\UserException::throwMessage()
     */
    protected function throwMessage(): string
    {
        return \sprintf("This credential '%s' for user '%s' is invalid",
            $this->credential,
            $this->user->getName());
    }
    
}
