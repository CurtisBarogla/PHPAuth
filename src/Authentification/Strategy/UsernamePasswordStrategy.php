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

namespace Zoe\Component\Security\Authentification\Strategy;

use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Encoder\PasswordEncoderInterface;

/**
 * Process a verification over the passwords
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UsernamePasswordStrategy implements AuthentificationStrategyInterface
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
     * @see \Zoe\Component\Security\Authentification\Strategy\AuthentificationStrategyInterface::process()
     */
    public function process(UserInterface $loadedUser, UserInterface $user): bool
    {
        return $this->encoder->compare($user->getPassword(), $loadedUser->getPassword());
    }
    
}
