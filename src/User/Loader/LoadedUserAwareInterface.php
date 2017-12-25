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

/**
 * Make aware a component of a user loaded by a UserLoader
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
interface LoadedUserAwareInterface
{
    
    /**
     * Get a user given by a user loader
     * 
     * @return AuthenticationUserInterface
     *   AuthenticationUserInterface given by a UserLoaderInterface implementation
     */
    public function getLoadedUser(): AuthenticationUserInterface;
    
    /**
     * Register a loaded user for the component
     * 
     * @param AuthenticationUserInterface $loadedUser
     *   Loaded user given by a UserLoaderInterface implementation
     */
    public function setLoadedUser(AuthenticationUserInterface $loadedUser): void;
    
}
