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
use Zoe\Component\Security\Storage\UserStorageAwareInterface;
use Zoe\Component\Security\Storage\UserStorageTrait;
use Zoe\Component\Security\User\StorableUser;
use Zoe\Component\Security\User\StorableUserInterface;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;

/**
 * Basic AuthenticationInterface implementation.
 * Authenticate users from UserLoader through various strategies and store results into UserStorage
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class Authentication implements AuthenticationInterface, UserStorageAwareInterface
{
    
    use UserStorageTrait;
    
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
     * @see \Zoe\Component\Security\Authentication\AuthenticationInterface::authenticate()
     */
    public function authenticate(UserInterface $user): void
    {
        $loadedUser = $this->loader->loadUser($user);

        if(!$this->strategy->process($loadedUser, $user))
            throw new AuthenticationFailedException(\sprintf("This user '%s' cannot be authenticated",
                $user->getName()));
        
        try {
            $this->getStorage()->refreshUser(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUser::createFromUser($loadedUser));
        } catch (UserNotFoundException $e) {
            $this->getStorage()->addUser(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUser::createFromUser($loadedUser));
        }
    }

}
