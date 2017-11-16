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

use Zoe\Component\Security\Encoder\PasswordEncoderInterface;
use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Contracts\CredentialUserInterface;

/**
 * Process a verification over the passwords
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UsernamePasswordStrategy implements AuthenticationStrategyInterface
{
    
    /**
     * Password encoder
     * 
     * @var PasswordEncoderInterface
     */
    private $encoder;
    
    /**
     * Initialize the strategy
     * 
     * @param PasswordEncoderInterface $encoder
     *   PasswordEncoderInterface instance
     */
    public function __construct(PasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */
    public function process(MutableUserInterface $loadedUser, UserInterface $user): int
    {
        if(!$loadedUser instanceof CredentialUserInterface || !$user instanceof CredentialUserInterface 
            || null === $user->getPassword() || null === $loadedUser->getPassword())
            return self::SKIP;
        
        return (true === $this->encoder->compare($user->getPassword(), $loadedUser->getPassword())) ? self::SUCCESS : self::FAIL;
    }
    
}
