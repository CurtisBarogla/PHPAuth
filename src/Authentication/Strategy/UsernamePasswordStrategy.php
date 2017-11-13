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

use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Encoder\PasswordEncoderInterface;

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
    public function process(UserInterface $loadedUser, UserInterface $user): int
    {
        if(null === $user->getPassword() || null === $loadedUser->getPassword())
            return self::SKIP;
        
        return (true === $this->encoder->compare($user->getPassword(), $loadedUser->getPassword())) ? self::SUCCESS : self::FAIL;
    }
    
}
