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

namespace Zoe\Component\Security\Collection\User;

use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\User\Loader\UserLoaderInterface;

/**
 * Collection of user loaders trying to load a user from various user loaders
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class UserLoaderCollection
{
    
    /**
     * UserLoader registered
     * 
     * @var UserLoaderInterface[]
     */
    private $loaders;
    
    /**
     * Add a loader to the collection
     * 
     * @param UserLoaderInterface $loader
     *   Loader instance
     */
    public function add(UserLoaderInterface $loader): void
    {
        $this->loaders[$loader->identify()] = $loader;
    }
    
    /**
     * Try to load a user from all setted loaders
     * 
     * @param UserInterface $user
     *   User to load
     * 
     * @return UserInterface
     *   User if one is found
     * 
     * @throws UserNotFoundException
     *   When no user has been found for the setted user loaders
     */
    public function getUser(UserInterface $user): UserInterface
    {
        $loaders = null;
        foreach ($this->loaders as $name => $loader) {
            try {
                return $loader->loadUser($user);  
            } catch (UserNotFoundException $e) {
                $loaders[] = $name;
                continue;
            }
        }
        
        throw new UserNotFoundException(\sprintf("This user '%s' has been not found into the setted loaders '%s'",
            $user->getName(),
            \implode(", ", $loaders)));
    }
    
}
