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
 * Shortcut to set LoadedUserAware a component
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
trait LoadedUserAwareTrait
{
    
    /**
     * User loaded
     * 
     * @var AuthenticationUserInterface
     */
    protected $loadedUser;
    
    /**
     * {@inheritdoc}
     * @see \Zoe\Component\Security\User\Loader\LoadedUserAwareInterface::getLoadedUser()
     */
    public function getLoadedUser(): AuthenticationUserInterface
    {
        return $this->loadedUser;
    }
    
    /**
     * {@inheritdoc}
     * @see \Zoe\Component\Security\User\Loader\LoadedUserAwareInterface::setLoadedUser()
     */
    public function setLoadedUser(AuthenticationUserInterface $loadedUser): void
    {
        $this->loadedUser = $loadedUser;
    }
    
}
