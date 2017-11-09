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

use Zoe\Component\Security\User\UserInterface;
use Zoe\Component\Security\Exception\UserNotFoundException;
use Zoe\Component\Security\User\User;

/**
 * Load users from a given array
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeUserLoader implements UserLoaderInterface
{
    
    /**
     * Array representing users
     * 
     * @var array
     */
    private $users;
    
    /**
     * Initialize the loader
     * 
     * @param array $users
     *   Array of users
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Loader\UserLoaderInterface::loadUser()
     */
    public function loadUser(UserInterface $user): UserInterface
    {
        $name = $user->getName();
        if(!isset($this->users[$name]))
            throw new UserNotFoundException(\sprintf("This user '%s' does not exist",
                $user->getName()));
        
        $attributes = $this->users[$name]["attributes"] ?? null;
        $roles = $this->users[$name]["roles"] ?? [];
        $root = $this->users[$name]["root"] ?? false;
        
        return new User($name, $user->getPassword(), $roles, $root, $attributes);
    }

}
