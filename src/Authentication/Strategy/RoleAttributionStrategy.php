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

namespace Zoe\Component\Security\Authentication\Strategy;

use Zoe\Component\Security\User\Contracts\MutableUserInterface;
use Zoe\Component\Security\User\Contracts\UserInterface;
use Zoe\Component\Security\Role\RoleCollection;

/**
 * Attribute roles to users based on a RoleCollection.
 * Attribute is only done based on the loaded one
 * 
 * @author CurtisBarogla <curtis_barogla@outlook.fr>
 *
 */
class RoleAttributionStrategy implements AuthenticationStrategyInterface
{
    
    /**
     * Role collection
     * 
     * @var RoleCollection
     */
    private $roles;
    
    /**
     * If roles are setted into both user (loaded one and given one)
     * 
     * @var bool
     */
    private $both;
    
    /**
     * Initialiaze strategy
     * 
     * @param RoleCollection $roles
     *   RoleCollection
     * @param bool $both
     *   Set to true to alter role of both users
     */
    public function __construct(RoleCollection $roles, bool $both = true)
    {
        $this->roles = $roles;
        $this->both = $both;
    }
    
    /**
     * {@inheritDoc}
     * @see \Zoe\Component\Security\Authentication\Strategy\AuthenticationStrategyInterface::process()
     */    
    public function process(MutableUserInterface $loadedUser, UserInterface $user): int
    {
        $roles = [];
        foreach ($loadedUser->getRoles() as $role) {
            $roles = \array_merge($this->roles->getRole($role), $roles);
        }
        $roles = \array_unique($roles);
        
        foreach ($roles as $role) {
            $loadedUser->addRole($role);
            if($user instanceof MutableUserInterface && $this->both)
                $user->addRole($role);
        }
        
        return self::SKIP;
    }

}
