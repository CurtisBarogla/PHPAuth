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
use Zoe\Component\Security\User\AuthenticationUser;

/**
 * Load user from simple array
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class NativeUserLoader implements UserLoaderInterface
{
    
    /**
     * Array containing all informations about all users
     * 
     * @var array
     */
    private $users;
    
    /**
     * Initialize user loader
     * 
     * @param array $users
     *   Arrays indexed by user name containing all informations about all users 
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\User\Loader\UserLoaderInterface::loadUser()
     */
    public function loadUser(AuthenticationUserInterface $user): AuthenticationUserInterface
    {
        $name = $user->getName();
        if(!isset($this->users[$name]))
            throw new UserNotFoundException($user, UserNotFoundException::LOADER_ERROR_CODE);
        
        $infos = $this->users[$name];
        
        return new AuthenticationUser(
            $name, 
            $infos["password"]      ?? null, 
            $infos["root"]          ?? false,
            $infos["attributes"]    ?? [],
            $infos["roles"]         ?? [],
            $infos["credentials"]   ?? []);
    }

}
