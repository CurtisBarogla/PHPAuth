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

use Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface;
use Zoe\Component\Security\Exception\AuthenticationFailedException;
use Zoe\Component\Security\Exception\LogicException;
use Zoe\Component\Security\User\UserFactory;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use Zoe\Component\Security\User\Contracts\StorableUserInterface;

/**
 * Basic AuthenticationInterface implementation.
 * Authenticate users from UserLoader through various strategies and store results into UserStorage
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
     * Initialize the authentication process
     * 
     * @param UserLoaderInterface $loader
     *   Responsible to load user
     * @param AuthenticationStrategyInterface $strategy
     *   Strategy handling authentication
     */
    public function __construct(UserLoaderInterface $loader, AuthenticationStrategyInterface $strategy)
    {
        $this->loader = $loader;
        $this->strategy = $strategy;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\AuthenticationInterface::switch()
     */
    public function switch(?UserLoaderInterface $loader, ?AuthenticationStrategyInterface $strategy): AuthenticationInterface
    {
        if(null === $loader && null === $strategy) {
            throw new LogicException("UserLoader and AuthenticationStrategy cannot be both null during switching process");
        }
        
        $loader = (null === $loader) ? $this->loader : $loader;
        $strategy = (null === $strategy) ? $this->strategy : $strategy;
        
        return new Authentication($loader, $strategy);
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\AuthenticationInterface::authenticate()
     */
    public function authenticate(UserInterface $user): StorableUserInterface
    {
        $loadedUser = $this->loader->loadUser($user);
  
        switch ($this->strategy->process($loadedUser, $user)) {
            case AuthenticationStrategyInterface::SUCCESS:
            case AuthenticationStrategyInterface::SHUNT_ON_SUCCESS:
                break;
            case AuthenticationStrategyInterface::SKIP:
            case AuthenticationStrategyInterface::FAIL:
                throw new AuthenticationFailedException(\sprintf("This user '%s' cannot be authenticated",
                    $user->getName()));
                break;
            default:
                throw new \UnexpectedValueException(\sprintf("Invalid return value on '%s' strategy",
                    \get_class($this->strategy)));
        }
        
        if(null !== $handled = $this->strategy->handle($loadedUser))
            $loadedUser = $handled;
        
        return UserFactory::createStorableUser($loadedUser);
    }

}
