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

namespace Zoe\Component\Security\Authentication\Strategy;

use Zoe\Component\Security\User\Loader\LoadedUserAwareInterface;
use Zoe\Component\Security\User\Loader\LoadedUserAwareTrait;
use Zoe\Component\Security\Password\PasswordHashingInterface;
use Zoe\Component\Security\User\AuthenticationUserInterface;

/**
 * Strategy checking password of an authentication user via an implementation of PasswordHashing
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class PasswordAuthenticationStrategy implements LoadedUserAwareInterface, AuthenticationStrategyInterface
{
    
    use LoadedUserAwareTrait;
    
    /**
     * Password hashing implementation 
     * 
     * @var PasswordHashingInterface
     */
    private $hashing;
    
    /**
     * Initialize strategy
     * 
     * @param PasswordHashingInterface $hashing
     *   Password hashing implementation
     */
    public function __construct(PasswordHashingInterface $hashing)
    {
        $this->hashing = $hashing;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */
    public function process(AuthenticationUserInterface $user): int
    {
        if(null === $user->getPassword() || null === $this->loadedUser->getPassword())
            return self::SKIP;
        
        return ($this->hashing->check($user->getPassword(), $this->loadedUser->getPassword())) ? self::SUCCESS : self::ERROR;
    }
    
}
