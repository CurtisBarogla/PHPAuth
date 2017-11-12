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

use Zoe\Component\Security\Storage\UserStorageAwareInterface;
use Zoe\Component\Security\Storage\UserStorageTrait;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Collection\User\UserLoaderCollection;
use Zoe\Component\Security\Collection\Strategy\AuthenticationStrategyCollection;
use Zoe\Component\Security\Exception\AuthenticationFailedException;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\StorableUserInterface;
use Zoe\Component\Security\User\StorableUser;

/**
 * Process authentication over collections
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class CollectionAuthentication implements AuthenticationInterface, UserStorageAwareInterface
{
    
    use UserStorageTrait;
    
    /**
     * Collection of user loaders
     * 
     * @var UserLoaderCollection
     */
    private $loaders;
    
    /**
     * Collection of strategies
     * 
     * @var AuthenticationStrategyCollection
     */
    private $strategies;
    
    /**
     * Initialize the authentication process
     * 
     * @param UserLoaderCollection $loaders
     *   Collection of user loaders
     * @param AuthenticationStrategyCollection $strategies
     *   Collection of strategies
     */
    public function __construct(UserLoaderCollection $loaders, AuthenticationStrategyCollection $strategies)
    {
        $this->loaders = $loaders;
        $this->strategies = $strategies;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\AuthenticationInterface::authenticate()
     */
    public function authenticate(UserInterface $user): void
    {
        $loadedUser = $this->loaders->getUser($user);
        
        if(!$this->strategies->process($loadedUser, $user))
            throw new AuthenticationFailedException(\sprintf("This user '%s' cannot be authenticated",
                $user->getName()));
            
        try {
            $this->getStorage()->refreshUser(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUser::createFromUser($loadedUser));
        } catch (UserNotFoundException $e) {
            $this->getStorage()->addUser(StorableUserInterface::USER_STORE_IDENTIFIER, StorableUser::createFromUser($loadedUser));
        }
    }
    
}
