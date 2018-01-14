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

namespace Zoe\Component\Authentication;

use Zoe\Component\User\AuthenticatedUserInterface;
use Zoe\Component\User\AuthenticationUserInterface;
use Zoe\Component\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\User\Loader\UserLoaderInterface;
use Zoe\Component\User\Exception\UserNotFoundException;
use Zoe\Component\Authentication\Exception\AuthenticationFailedException;
use Zoe\Component\User\AuthenticatedUser;

/**
 * Native implementation of AuthenticationInterface.
 * Base authentication over AuthenticationStrategy
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Authentication implements AuthenticationInterface
{

    /**
     * Strategy used to authenticate a user
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
     * Initialize authentication process
     * 
     * @param AuthenticationStrategyInterface $strategy
     *   Strategy used to authenticate the user
     * @param UserLoaderInterface $loader
     *   User loader 
     */
    public function __construct(AuthenticationStrategyInterface $strategy, UserLoaderInterface $loader)
    {
        $this->strategy = $strategy;
        $this->loader = $loader;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Authentication\AuthenticationInterface::authenticate()
     */
    public function authenticate(AuthenticationUserInterface $user): AuthenticatedUserInterface
    {
        try {
            $loaded = $this->loader->load($user);
            $this->strategy->setUser($loaded);
            
            switch ($this->strategy->process($user)) {
                case AuthenticationStrategyInterface::SKIP:
                case AuthenticationStrategyInterface::ERROR:
                    throw new AuthenticationFailedException(\sprintf("This user '%s' cannot be authenticated",
                        $user->getName()));
                break;
                case AuthenticationStrategyInterface::SUCCESS:
                    return AuthenticatedUser::createFromAuthenticationUser($loaded);
                break;
                default:
                    throw new \LogicException(\sprintf("Authentication strategy '%s' returned an invalid value",
                        \get_class($this->strategy)));
            }
        } catch (UserNotFoundException $e) {
            throw new AuthenticationFailedException(\sprintf("This user '%s' has been not found",
                $user->getName()));
        }
    }
    
    /**
     * Modify the strategy used to authenticate a user
     * 
     * @param AuthenticationStrategyInterface $strategy
     *   New strategy
     * 
     * @return AuthenticationInterface
     *   Authentication process with updated strategy setted
     */
    public function via(AuthenticationStrategyInterface $strategy): AuthenticationInterface
    {
        return new Authentication($strategy, $this->loader);
    }
    
}
