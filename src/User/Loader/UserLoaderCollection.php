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

namespace Zoe\Component\Security\User\Loader;

use Zoe\Component\Security\User\AuthenticationUserInterface;
use Zoe\Component\Security\Exception\User\UserNotFoundException;

/**
 * Try to load a user over multiple UserLoaderInterface implementations
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderCollection implements UserLoaderInterface
{
    
    /**
     * Registered loaders
     * 
     * @var UserLoaderInterface[]
     */
    private $loaders;
    
    /**
     * Initialize loader
     * 
     * @param UserLoaderInterface $defaultLoader
     *   Default user loader
     */
    public function __construct(UserLoaderInterface $defaultLoader)
    {
        $this->loaders[] = $defaultLoader;
    }
    
    /**
     * Add a loader to the collection
     * 
     * @param UserLoaderInterface $loader
     *   User loader
     */
    public function addLoader(UserLoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Loader\UserLoaderInterface::loadUser()
     */
    public function loadUser(AuthenticationUserInterface $user): AuthenticationUserInterface
    {
        foreach ($this->loaders as $loader) {
            try {
                return $loader->loadUser($user);
            } catch (UserNotFoundException $e) {
                continue;
            }
        }
        
        throw new UserNotFoundException($user, UserNotFoundException::LOADER_ERROR_CODE);
    }
    
}
