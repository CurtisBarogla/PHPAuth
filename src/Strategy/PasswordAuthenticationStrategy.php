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

namespace Zoe\Component\Authentication\Strategy;

use Zoe\Component\User\AuthenticationUserInterface;
use Zoe\Component\User\Loader\LoadedUserAwareTrait;
use Zoe\Component\Password\Hash\PasswordHashInterface;

/**
 * Authenticate user via its password
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordAuthenticationStrategy implements AuthenticationStrategyInterface
{
    
    use LoadedUserAwareTrait;
    
    /**
     * Password hash
     * 
     * @var PasswordHashInterface
     */
    private $hash;
    
    /**
     * Initialize strategy
     * 
     * @param PasswordHashInterface $hash
     *   Hash password handler
     */
    public function __construct(PasswordHashInterface $hash)
    {
        $this->hash = $hash;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */
    public function process(AuthenticationUserInterface $user): int
    {
        if(null === $this->user->getPassword() || null === $user->getPassword())
            return self::SKIP;
        
        return $this->hash->isValid($user->getPassword(), $this->user->getPassword()) ? self::SUCCESS : self::ERROR;
    }

}
