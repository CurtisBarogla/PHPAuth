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
 
namespace Ness\Component\Authentication\Strategy;

use Ness\Component\Authentication\User\AuthenticationUserInterface;
use Ness\Component\Password\Hash\PasswordHashInterface;
use Ness\Component\Password\Password;

/**
 * Authenticate an user over its password over the Password component
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordAuthenticationStrategy extends AbstractAuthenticationStrategy
{
    
    /**
     * Password component
     * 
     * @var PasswordHashInterface
     */
    private $password;
    
    /**
     * Initialize strategy
     * 
     * @param PasswordHashInterface $password
     *   Hash password component
     */
    public function __construct(PasswordHashInterface $password)
    {
        $this->password = $password;
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */
    public function process(AuthenticationUserInterface $user): int
    {
        return $this->password->verify(new Password($user->getPassword()), $this->getLoadedUser()->getPassword()) ? self::SUCCESS : self::ERROR;
    }
    
}
