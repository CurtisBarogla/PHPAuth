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

namespace Zoe\Component\Security\Authentication;

use Zoe\Component\Security\User\AuthenticatedUserInterface;
use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use Zoe\Component\Security\Exception\User\UserNotFoundException;
use Zoe\Component\Security\Exception\Authentication\AuthenticationFailedException;
use Zoe\Component\Security\User\Loader\LoadedUserAwareInterface;
use Zoe\Component\Security\User\AuthenticatedUser;

/**
 * Native implementation of AuthenticationInterface.
 * This implementation authenticate a user via a user given by a UserLoader implementation and a given AuthenticationStrategy implementation
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Authentication implements AuthenticationInterface
{
    
    /**
     * Strategy to authenticate the user
     * 
     * @var AuthenticationStrategyInterface
     */
    private $strategy;
    
    /**
     * User loader
     * 
     * @var UserLoaderInterface
     */
    private $loader;
    
    /**
     * When error comes from the user loader
     * 
     * @var int
     */
    public const USER_LOADER_ERROR = 1;
    
    /**
     * When the strategy failed on the AuthenticationUser
     * 
     * @var int
     */
    public const AUTHENTICATION_STRATEGY_ERROR = 2;
    
    /**
     * Initialize the authentication process
     * 
     * @param UserLoaderInterface $loader
     *   User loader
     * @param AuthenticationStrategyInterface $strategy
     *   Strategy to authenticate the user
     */
    public function __construct(UserLoaderInterface $loader, AuthenticationStrategyInterface $strategy)
    {
        $this->loader = $loader;
        $this->strategy = $strategy;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\AuthenticationInterface::authenticate()
     */
    public function authenticate(AuthenticationUserInterface $user): AuthenticatedUserInterface
    {
        try {
            $loaded = $this->loader->loadUser($user);
            
            if($this->strategy instanceof LoadedUserAwareInterface)
                $this->strategy->setLoadedUser($loaded);
            
            if($this->strategy->process($user) !== AuthenticationStrategyInterface::SUCCESS)
                throw new AuthenticationFailedException(\sprintf("This user '%s' cannot be authenticated as the strategy given failed",
                    $user->getName()), self::AUTHENTICATION_STRATEGY_ERROR);
                
            return new AuthenticatedUser(
                $loaded->getName(), 
                new \DateTime(),
                $loaded->isRoot(),
                $loaded->getAttributes(),
                $loaded->getRoles());
        } catch (UserNotFoundException $e) {
            throw new AuthenticationFailedException(
                \sprintf("This user '%s' cannot be authenticated as it cannot be loaded by the given user loader",
                    $user->getName()), self::USER_LOADER_ERROR);
        }
    }

}
