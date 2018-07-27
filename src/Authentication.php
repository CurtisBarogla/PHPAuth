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
 
namespace Ness\Component\Authentication;

use Ness\Component\Authentication\User\AuthenticatedUserInterface;
use Ness\Component\Authentication\User\AuthenticationUserInterface;
use Ness\Component\User\UserInterface;
use Ness\Component\User\Loader\UserLoaderInterface;
use Ness\Component\Authentication\Strategy\AuthenticationStrategyInterface;
use Ness\Component\User\Exception\UserNotFoundException;
use Ness\Component\Authentication\Exception\AuthenticationFailedException;
use Ness\Component\Authentication\User\AuthenticatedUser;
use Ness\Component\Authentication\User\AuthenticationUser;
use Ness\Component\Authentication\Exception\UserCredentialNotFoundException;

/**
 * Basic implementation of AuthenticationInterface based on setted AuthenticationStrategy and UserLoader.
 * This implementation handles missing credential/attribute as if a required one into given strategy is missing, authentication process will result an error
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Authentication implements AuthenticationInterface
{
    
    /**
     * User loader
     * 
     * @var UserLoaderInterface
     */
    private $loader;
    
    /**
     * Strategy used to authenticate user
     * 
     * @var AuthenticationStrategyInterface
     */
    private $strategy;
    
    /**
     * Exception code when user cannot be loaded by given loader
     * 
     * @var int
     */
    public const USER_NOT_FOUND = 1;
    
    /**
     * Exception code when an attribute or a credential has been not setted into the user
     * 
     * @var int
     */
    public const STRATEGY_ERROR = 2;
    
    /**
     * Exception when strategy found the attribute or credential when failed the process
     * 
     * @var int
     */
    public const STRATEGY_FAILED = 3;
    
    /**
     * Initialize authentiation
     * 
     * @param UserLoaderInterface $loader
     *   User loader
     * @param AuthenticationStrategyInterface $strategy
     *   Authentication strategy
     */
    public function __construct(UserLoaderInterface $loader, AuthenticationStrategyInterface $strategy)
    {
        $this->loader = $loader;
        $this->strategy = $strategy;
    }
    
    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\AuthenticationInterface::authenticate()
     */
    public function authenticate(UserInterface $user): AuthenticatedUserInterface
    {
        try {
            $loaded = AuthenticationUser::initializeFromUser($this->loader->loadUser($user->getName()));
            $this->strategy->setLoadedUser($loaded);
            
            try {
                if($this->strategy->process(
                    (!$user instanceof AuthenticationUserInterface) 
                        ? AuthenticationUser::initializeFromUser($user) 
                        : $user) === AuthenticationStrategyInterface::SUCCESS) {
                    return AuthenticatedUser::initializeFromUser($loaded);
                }
                
                throw new AuthenticationFailedException("This user '{$user->getName()}' cannot be authenticated by Authentication component as given strategy failed", self::STRATEGY_FAILED);
            } catch (UserCredentialNotFoundException $e) {
                throw new AuthenticationFailedException("This user '{$user->getName()}' cannot be authenticated by Authentication component as no strategy can handle it", self::STRATEGY_ERROR);
            }
            
        } catch (UserNotFoundException $e) {
            throw new AuthenticationFailedException("This user '{$user->getName()}' cannot be authenticated as given UserLoader cannot found one", self::USER_NOT_FOUND);
        }
    }

    /**
     * {@inheritDoc}
     * @see \Ness\Component\Authentication\AuthenticationInterface::isAuthenticated()
     */
    public function isAuthenticated(UserInterface $user): bool
    {
        return $user instanceof AuthenticatedUserInterface;
    }

}
